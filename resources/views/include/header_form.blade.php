<center>
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-2">
            <img src="{{ asset('resources/img/doh.png') }}" width="100">
        </div>
        <div class="col-md-8">
            <div class="align small-text" style="font-size: 12pt;">
                Republic of the Philippines<br>
                DEPARTMENT OF HEALTH<br>
                <strong>CENTRAL VISAYAS CENTER for HEALTH DEVELOPMENT</strong><br>
                Osmeña Boulevard,Sambag II,Cebu City, 6000 Philippines<br>
                Regional Director’s Office Tel. No. (032) 253-6355 Fax No. (032) 254-0109<br>
                Official Website: <a href="http://www.ro7.doh.gov.ph" target="_blank">http://www.ro7.doh.gov.ph</a> Email Address: dohro7@gmail.com<br>
            </div>
        </div>
        <div class="col-md-2">
            <img src="{{ asset('resources/img/f1.jpg') }}" width="100">
        </div>
    </div>
    @if($form_type == 'normal')
        <h3 class="text-green clinical-form-title">Clinical Referral Form</h3>
    @else
        <h3 class="text-green clinical-form-title">BEmONC/ CEmONC REFERRAL FORM</h3>
    @endif
</center>
