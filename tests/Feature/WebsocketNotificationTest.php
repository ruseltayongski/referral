<?php

namespace Tests\Feature;

use App\Events\NewReferral;
use App\Events\SocketReco;
use App\Http\Controllers\WebsocketNotificationController;
use App\Services\WebsocketNotificationService;
use App\WebsocketNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class WebsocketNotificationTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('facility_id');
            $table->string('status');
            $table->string('level');
            $table->integer('subopd_id')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->integer('patient_id')->nullable();
        });
        Schema::create('facility_assignment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('facility_id');
            $table->string('status');
        });
        Schema::create('activity', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('status');
            $table->integer('referred_from')->nullable();
            $table->integer('referred_to')->nullable();
            $table->integer('referring_md')->nullable();
        });
        Schema::create('tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('patient_id');
            $table->integer('referring_md')->nullable();
        });
        Schema::create('feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->integer('sender');
            $table->text('message')->nullable();
        });
        Schema::create('websocket_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('recipient_level', 50)->nullable();
            $table->string('event_type', 50);
            $table->string('title', 100);
            $table->string('body', 500);
            $table->string('related_code', 100)->nullable();
            $table->unsignedInteger('related_id')->nullable();
            $table->string('destination_url', 500)->nullable();
            $table->char('notification_key', 64);
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function testNewReferralTargetsActiveUsersAtAssignedFacilityAndDeduplicates()
    {
        $this->addUser(1, 10, 'active', 'doctor', 5);
        $this->addUser(2, 20, 'active', 'doctor', 5);
        $this->addUser(3, 10, 'active', 'doctor', 6);
        $this->addUser(4, 10, 'inactive', 'doctor', 5);
        $this->addUser(5, 10, 'active', 'Patient', 5);
        $this->addUser(6, 10, 'active', 'support', 5);
        $this->addUser(7, 30, 'active', 'support', 5);
        DB::table('facility_assignment')->insert([
            ['user_id' => 2, 'facility_id' => 10, 'status' => 'Active'],
            ['user_id' => 7, 'facility_id' => 10, 'status' => 'Active'],
        ]);

        $event = new NewReferral([
            'patient_code' => 'NT-001',
            'patient_name' => 'Sample Patient',
            'referring_md' => 'Referring Doctor',
            'referring_name' => 'Origin Hospital',
            'referred_to' => 10,
            'telemedicine' => 1,
            'subOpdId' => 5,
            'status' => 'referred',
            'tracking_id' => 100,
            'referred_date' => '2026-09-30 10:00:00',
        ]);

        event($event);
        event($event);

        $this->assertSame(2, WebsocketNotification::count());
        $this->assertDatabaseHas('websocket_notifications', [
            'user_id' => 1,
            'recipient_level' => 'doctor',
            'event_type' => 'new_referral',
            'related_code' => 'NT-001',
        ]);
        $this->assertDatabaseHas('websocket_notifications', [
            'user_id' => 2,
            'related_code' => 'NT-001',
        ]);
        $this->assertDatabaseMissing('websocket_notifications', ['user_id' => 3]);
        $this->assertDatabaseMissing('websocket_notifications', ['user_id' => 4]);
        $this->assertDatabaseMissing('websocket_notifications', ['user_id' => 5]);
        $this->assertDatabaseMissing('websocket_notifications', ['user_id' => 6]);
        $this->assertDatabaseMissing('websocket_notifications', ['user_id' => 7]);
    }

    public function testRecoNotificationReferencesStoredMessageAndExcludesSenderFacility()
    {
        $this->addUser(1, 10, 'active', 'doctor', null);
        $this->addUser(2, 20, 'active', 'doctor', null);
        $this->addUser(3, 30, 'active', 'nurse', null);
        DB::table('activity')->insert([
            ['code' => 'RC-001', 'status' => 'referred', 'referred_from' => 10, 'referred_to' => 20],
            ['code' => 'RC-001', 'status' => 'transferred', 'referred_from' => 20, 'referred_to' => 30],
        ]);
        DB::table('feedback')->insert([
            'id' => 42,
            'code' => 'RC-001',
            'sender' => 1,
            'message' => 'New text',
        ]);

        (new WebsocketNotificationService())->record(new SocketReco([
            'code' => 'RC-001',
            'sender_facility' => 10,
            'userid_sender' => 1,
            'name_sender' => 'Dr Sender',
            'message' => '<b>New text</b>',
            'date_now' => '2026-09-30 10:05:00',
        ]));

        $this->assertSame(2, WebsocketNotification::count());
        $notification = WebsocketNotification::where('user_id', 2)->first();
        $this->assertSame('reco_message', $notification->event_type);
        $this->assertSame(42, (int) $notification->related_id);
        $this->assertSame('Dr Sender: New text', $notification->body);
        $this->assertSame(url('reco'), $notification->destination_url);
        $this->assertDatabaseMissing('websocket_notifications', ['user_id' => 1]);
    }

    public function testNewReferralFallsBackToDoctorSavedOnReferralActivity()
    {
        $this->addUser(10, 10, 'active', 'doctor', null);
        $this->addUser(11, 20, 'active', 'doctor', null);
        DB::table('users')->where('id', 10)->update(['fname' => 'Alex', 'lname' => 'Doctor']);
        DB::table('activity')->insert([
            'code' => 'DOC-001',
            'status' => 'referred',
            'referred_from' => 10,
            'referred_to' => 20,
            'referring_md' => 10,
        ]);

        (new WebsocketNotificationService())->record(new NewReferral([
            'patient_code' => 'DOC-001',
            'patient_name' => 'Sample Patient',
            'referred_to' => 20,
            'status' => 'referred',
        ]));

        $this->assertDatabaseHas('websocket_notifications', [
            'user_id' => 11,
            'related_code' => 'DOC-001',
            'body' => 'Sample Patient was referred by Dr. Alex Doctor',
        ]);
    }

    public function testTransferredReferralDestinationDependsOnRecipientFacility()
    {
        $this->addUser(1, 10, 'active', 'doctor', null);
        $this->addUser(2, 20, 'active', 'doctor', null);

        (new WebsocketNotificationService())->record(new NewReferral([
            'patient_code' => 'ROUTE-001',
            'patient_name' => 'Route Patient',
            'referring_md' => 'Referring Doctor',
            'referred_from' => 20,
            'referred_to' => 10,
            'telemedicine' => 0,
            'status' => 'transferred',
            'tracking_id' => 300,
            'referred_date' => '2026-09-30 10:30:00',
        ]));

        $this->assertSame(
            url('doctor/referral?filterRef=0&search=ROUTE-001'),
            WebsocketNotification::where('user_id', 1)->value('destination_url')
        );
        $this->assertSame(
            url('doctor/referred?filterRef=0&referredCode=ROUTE-001'),
            WebsocketNotification::where('user_id', 2)->value('destination_url')
        );
    }

    public function testPatientReceivesOnlyNotificationsForTheirOwnReferral()
    {
        $this->addUser(51, 0, 'active', 'Patient', null, 501);
        $this->addUser(52, 0, 'active', 'Patient', null, 502);
        DB::table('tracking')->insert([
            'code' => 'PAT-001',
            'patient_id' => 501,
        ]);

        (new WebsocketNotificationService())->record(new NewReferral([
            'patient_code' => 'PAT-001',
            'patient_name' => 'My Referral',
            'status' => 'referred',
        ]));

        $this->assertDatabaseHas('websocket_notifications', [
            'user_id' => 51,
            'recipient_level' => 'Patient',
            'related_code' => 'PAT-001',
        ]);
        $this->assertDatabaseMissing('websocket_notifications', [
            'user_id' => 52,
            'related_code' => 'PAT-001',
        ]);
    }

    public function testPatientReceivesRecoMessagesForTheirReferralButNotTheirOwnMessage()
    {
        $this->addUser(51, 0, 'active', 'Patient', null, 501);
        $this->addUser(52, 0, 'active', 'Patient', null, 502);
        DB::table('tracking')->insert([
            'code' => 'PAT-RECO-001',
            'patient_id' => 501,
        ]);
        DB::table('activity')->insert([
            'code' => 'PAT-RECO-001',
            'status' => 'referred',
            'referred_from' => 10,
            'referred_to' => 20,
        ]);
        DB::table('feedback')->insert([
            'id' => 61,
            'code' => 'PAT-RECO-001',
            'sender' => 99,
            'message' => 'Please review the referral',
        ]);

        $service = new WebsocketNotificationService();
        $service->record(new SocketReco([
            'code' => 'PAT-RECO-001',
            'sender_facility' => 20,
            'userid_sender' => 99,
            'name_sender' => 'Dr Sender',
            'message' => 'Please review the referral',
            'date_now' => '2026-09-30 11:00:00',
        ]));

        $this->assertDatabaseHas('websocket_notifications', [
            'user_id' => 51,
            'recipient_level' => 'Patient',
            'event_type' => 'reco_message',
            'related_code' => 'PAT-RECO-001',
            'related_id' => 61,
        ]);
        $this->assertDatabaseMissing('websocket_notifications', [
            'user_id' => 52,
            'related_code' => 'PAT-RECO-001',
        ]);

        $service->record(new SocketReco([
            'code' => 'PAT-RECO-001',
            'sender_facility' => 0,
            'userid_sender' => 51,
            'name_sender' => 'Patient Sender',
            'message' => 'My own message',
            'date_now' => '2026-09-30 11:01:00',
            'reco_id' => 62,
        ]));

        $this->assertSame(1, WebsocketNotification::where('user_id', 51)->count());
    }

    public function testReadEndpointCannotChangeAnotherUsersNotification()
    {
        $this->addUser(1, 10, 'active', 'doctor', null);
        $this->addUser(2, 20, 'active', 'doctor', null);
        $first = $this->createNotification(1);
        $other = $this->createNotification(2);
        Session::put('auth', (object) ['id' => 1, 'level' => 'doctor']);

        $controller = new WebsocketNotificationController();
        $otherResponse = $controller->markRead($other->id);
        $this->assertFalse($otherResponse->getData(true)['updated']);
        $this->assertNull($other->fresh()->read_at);

        $ownResponse = $controller->markRead($first->id);
        $this->assertTrue($ownResponse->getData(true)['updated']);
        $this->assertNotNull($first->fresh()->read_at);
    }

    public function testClearAllNotificationsDeletesCurrentUsersNotifications()
    {
        $this->addUser(1, 10, 'active', 'doctor', null);
        $this->addUser(2, 20, 'active', 'doctor', null);
        $this->createNotification(1);
        $this->createNotification(1, 'doctor');
        $this->createNotification(2);
        Session::put('auth', (object) ['id' => 1, 'level' => 'doctor']);

        $response = (new WebsocketNotificationController())->markAllRead();

        $this->assertTrue($response->getData(true)['deleted']);
        $this->assertSame(0, WebsocketNotification::where('user_id', 1)->count());
        $this->assertSame(1, WebsocketNotification::where('user_id', 2)->count());
    }

    public function testNotificationRoleMustMatchCurrentAccountRole()
    {
        $this->addUser(1, 10, 'active', 'doctor', null);
        $notification = $this->createNotification(1, 'Patient');
        Session::put('auth', (object) ['id' => 1, 'level' => 'doctor']);

        $response = (new WebsocketNotificationController())->fetch(
            Request::create('/websocket-notifications/fetch', 'GET'),
            new WebsocketNotificationService()
        );

        $this->assertSame([], $response->getData(true)['items']);
        $this->assertSame(0, $response->getData(true)['unread_count']);
        $this->assertNull($notification->fresh()->read_at);
    }

    private function addUser($id, $facilityId, $status, $level, $subopdId, $patientId = null)
    {
        DB::table('users')->insert([
            'id' => $id,
            'facility_id' => $facilityId,
            'status' => $status,
            'level' => $level,
            'subopd_id' => $subopdId,
            'patient_id' => $patientId,
        ]);
    }

    private function createNotification($userId, $recipientLevel = 'doctor')
    {
        return WebsocketNotification::create([
            'user_id' => $userId,
            'recipient_level' => $recipientLevel,
            'event_type' => 'new_referral',
            'title' => 'New referral',
            'body' => 'Patient was referred',
            'related_code' => 'NT-' . $userId,
            'destination_url' => url('doctor/referred'),
            'notification_key' => hash('sha256', 'user-' . $userId),
        ]);
    }
}