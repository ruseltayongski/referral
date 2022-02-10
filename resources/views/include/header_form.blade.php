<center>
    <div class="web-view">
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
        @if($form_type == 'normal' || !isset($form_type))
            <h3 class="text-green clinical-form-title" style="font-size: 23pt;">CLINICAL REFERRAL FORM</h3>
        @else
            <h3 class="text-green clinical-form-title" style="font-size: 23pt;">BEmONC/ CEmONC REFERRAL FORM</h3>
        @endif
    </div>
    <div class="mobile-view">
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-4">
                <img class="pull-left" src="{{ asset('resources/img/doh.png') }}" width="50">
                <img class="pull-right" src="{{ asset('resources/img/f1.jpg') }}" width="50">
            </div>
            <div class="col-md-8">
                <div class="align small-text" style="font-size: 11pt;">
                    Republic of the Philippines<br>
                    DEPARTMENT OF HEALTH<br>
                    <strong>CENTRAL VISAYAS CENTER for HEALTH DEVELOPMENT</strong><br>
                    Osmeña Boulevard,Sambag II,Cebu City, 6000 Philippines<br>
                    Regional Director’s Office Tel. No. (032) 253-6355 <br> Fax No. (032) 254-0109<br>
                    Official Website: <a href="http://www.ro7.doh.gov.ph" target="_blank">http://www.ro7.doh.gov.ph</a><br>
                    Email Address: dohro7@gmail.com<br>
                </div>
            </div>
        </div>
        @if($form_type == 'normal' || !isset($form_type))
            <h3 class="text-green clinical-form-title" style="font-size: 18pt;">CLINICAL REFERRAL FORM</h3>
        @else
            <h3 class="text-green clinical-form-title" style="font-size: 18pt;">BEmONC/ CEmONC REFERRAL FORM</h3>
        @endif
    </div>
</center>
