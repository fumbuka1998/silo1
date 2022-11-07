<?php
$this->load->view('includes/letterhead');
?>
<h3 style="text-align: center">
    <?= nl2br('DEPLOYMENT PLAN MANAGEMENT
    (Usimamizi wa mpango wa safari)') ?>
</h3>
<table width="100%" border="" cellspacing="0" style="font-size: 12px">
        <thead>
            <tr style="background-color: #00aced; font-max-size: 40px;">
                <th style="text-align: left;"><?= nl2br('Deployment
                    (Kupelekwa)') ?></th><th style="text-align: left;"><?= nl2br('Driver
                    (Dereva)') ?></th><th style="text-align: left;"><?= nl2br('Vehicle Reg No.
                    (No. ya Gari)') ?></th><th style="text-align: left;"><?= nl2br('Dep Date
                    (Tar ya Kuondoka)') ?></th><th style="text-align: left;"><?= nl2br('Resting
                    (Kupumzika)') ?></th><th style="text-align: left;"><?= nl2br('Arrival Date
                    Tar ya Kufika)') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $deployment->name ?></td>
                <td><?= $deployment->driver ?></td>
                <td><?= $deployment->registration_number ?></td>
                <td><?= set_date($deployment->departure_time )?></td>
                <td><?= $deployment->relax_station ?></td>
                <td><?= set_date($deployment->arrival_time) ?></td>
            </tr>
            <tr>
                <td colspan="6">
                    <table border="" cellspacing="0" style="font-size: 12px;">
                        <thead>
                        <tr style="background-color: #00aced; font-max-size: 40px;">
                            <th style="width: 5%;">S/N</th><th style="width: 40%"><?= nl2br('Questionaire
(Maswali)')?></th><th style="text-align: left;"><?= nl2br('Answers
(Majibu)') ?></th><th style="text-align: left; width: 45%"><?= nl2br('Descriptions
(Maelezo)') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $key = 1;
                        foreach ($deployment->deployment_category_parameters() as $row){
                            $parameter = $row->category_parameter();
                            ?>

                            <tr>
                                <td><?= $key++ ?></td>
                                <td><?= $parameter->name ?></td>
                                <td><?= $row->answer ?></td>
                                <td style="width: 40%"><?= $row->description ?></td>
                            </tr>

                            <?php
                        }
                        ?>

                        </tbody>
                    </table>
                    <br/>
                </td>
            </tr>
        </tbody>
</table>
<br/>
<br/>
<table width="100%"  border="" cellspacing="0" style="font-size: 12px;">
    <thead>
    <tr style="background-color: #00aced; font-max-size: 40px;">
        <th style="text-align: left; width: 5%">S/N</th><th style="text-align: left; width: 95%"><?= nl2br('Passengers Names
(Majina ya abiria)') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $key = 1;
    foreach ($deployment->deployment_persons() as $row){
        ?>

        <tr>
            <td><?= $key++ ?></td>
            <td><?= $row->name ?></td>
        </tr>

        <?php
    }
    ?>

    </tbody>
</table>

<br/>
<br/>
<table width="100%"  border="" cellspacing="0" style="font-size: 12px">
    <thead>
    <tr style="background-color: #00aced; font-max-size: 40px;">
        <th style="text-align: left; width: 10%;"></th><th style="text-align: left;"><?= nl2br('Safe Driving Rules
(Sheria salama za uendeshaji)') ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/alkohol1.jpg') ?>" width="30px" height="30px"><img></td>
        <td>No alkohol or drugs while while working or driving .</td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/phone1.jpg') ?>" width="30px" height="30px"><img></td>
        <td>Do not use your phone or exceed speed limits while deiving</td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/belt3.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>wear your seat belt ,Follow prescibed Journey Manangement Plans</td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/pasenger.jpg') ?>" width="30px" height="30px"><img></td>
        <td>Secure your loads, Never carry passengers at the pick-up rear</td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td>Saini ya Dereva</td>
        <td>.................................................................................</td>
    </tr>
    </tfoot>
</table>

<br/>
<br/>
<div style="border:thin">
<textarea style="border:solid; width: 100%;">
Tumia ukurasra huu kufafanua kwa ujumla wa hatari kwenye safari na namna utakavyoweza kudhibiti ajali isitokee.
</textarea>
</div>

<br/>
<br/>
<table width="100%"   style="font-size: 12px" border="1" cellspacing="0">
    <caption>Vehicle Safety Inspection Checklist. If no defects found write "OK",If found write "X" and if not applicable write "N/A"</caption>
    <thead>
    <tr>
        <th colspan="2"><?= '&nbsp;'?></th>
        <th>Type
        <th>Week 1
        <th>Week 2
        <th>Week 3
        <th>Week 4
        <th>Week 5
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/cali.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Oil</td>
        <td>Engine</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/cali.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Oil</td>
        <td>Transmission</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/brake.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Brakes</td>
        <td>Park</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/brake.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Brakes</td>
        <td>Foot</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/light.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Light</td>
        <td>Brake</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/light.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Light</td>
        <td>Park</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/light.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Light</td>
        <td>Head</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/light.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Light</td>
        <td>Indicator</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/light.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Light</td>
        <td>Hazards</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/windowscreen.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Windscreen</td>
        <td>Screen</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/windowscreen.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Windscreen</td>
        <td>Wipers</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/battery.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Battery</td>
        <td>Terminals</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/battery.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Battery</td>
        <td>Water</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/steering.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Steering</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/hub.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Tyres</td>
        <td>Hub</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/tyre1.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Tyres</td>
        <td>Tyres</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/studs.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Tyres</td>
        <td>Nuts/Studs</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/tyre1.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Tyres</td>
        <td>Tyre Make</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/tyre1.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Tyres</td>
        <td>Spare</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/loadbin.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Loadbin</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/horn.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Horn</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/exhaust.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Exhaust</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/fanbelt.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Fan Belts</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/radiator.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Radiator</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/shockabsorber.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Shock Absorber</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/fueleak.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Fuel Leaks</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/differential.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Defferentials</td>
        <td>Rear</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/rearmirror.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Mirror</td>
        <td>Rear</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/sidemirror.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Mirror</td>
        <td>Side</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/seatbelt1.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Safety</td>
        <td>Seatbelts</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/kit.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Safety</td>
        <td>FA KIT</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/fire.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Safety</td>
        <td>Fire Extinguisher</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/logbook.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Safety</td>
        <td>Veh Log Book</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/triangle1.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Safety</td>
        <td>Triangle</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/wheelspanner.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Safety</td>
        <td>Wheel Spanner</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/jack.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Safety</td>
        <td>Jack</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/engine.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Engine Condition</td>
        <td>Clean</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/engine.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Engine Condition</td>
        <td>Dirty</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    <tr>
        <td><img src="<?= base_url('images/safety_signs_thumbnails/engine.jpg') ?>" alt="belt" width="30px" height="30px"><img></td>
        <td>Engine Condition</td>
        <td>Oil Leaks</td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
        <td><?= '&nbsp;' ?></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3" style="text-align: right"><strong>Driver Signature</strong></td>
        <?php for($i = 0; $i< 5; $i++){ ?>
            <td>...................</td>
        <?php } ?>
    </tr>
    <tr>
        <th colspan="3" style="text-align: right"><strong>Date</strong></th>
        <?php for($i = 0; $i< 5; $i++){ ?>
            <td>...................</td>
        <?php } ?>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right"><strong>Superviser Signature</strong></td>
        <?php for($i = 0; $i< 5; $i++){ ?>
            <td>...................</td>
        <?php } ?>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right"><strong>Date</strong></td>
        <?php for($i = 0; $i< 5; $i++){ ?>
            <td>...................</td>
        <?php } ?>
    </tr>
    </tfoot>
</table>

<br/>
<br/>
<table width="100%"  border="1" cellspacing="0" style="font-size: 12px">
    <thead>
    <tr>
        <th colspan="5">DEVIATIONS/MAPUNGUFU</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Date/Tarehe</td>
        <td>Description/Maelezo</td>
        <td>Action by whom/Nani nAfanye</td>
        <td>Target Date/Tarehe lengwa</td>
        <td>Complete Y/N Kamili Ndio/Hapana</td>
    </tr>
    <?php for($y=1; $y<=6;$y++)
    {
        ?>
        <tr>
            <td><?= '&nbsp;' ?></td>
            <td style="width: 40%"><?= '&nbsp;' ?></td>
            <td><?= '&nbsp;' ?></td>
            <td><?= '&nbsp;' ?></td>
            <td><?= '&nbsp;' ?></td>
        </tr>
    <?php } ?>

    </tbody>
</table>


