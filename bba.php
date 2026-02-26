<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// SESSION START
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// LOGIN CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// DATABASE
require_once "database.php";

// USER ID
$uid = (int) $_SESSION['user_id'];

// FETCH USER
$q = mysqli_query($con, "
        SELECT user_name, user_email, user_department, user_image
        FROM user_master
        WHERE user_id = $uid
    ");

$user = mysqli_fetch_assoc($q) ?? [];

// SAFE VARIABLES
$user_name = $user['user_name'] ?? '';
$user_email = $user['user_email'] ?? '';
$user_department = $user['user_department'] ?? '';
$user_image = $user['user_image'] ?? '';

// PROFILE IMAGE
$uploadPath = __DIR__ . "/upload/user/" . $user_image;
if (!empty($user_image) && file_exists($uploadPath)) {
    $profile_img = "upload/user/" . $user_image;
} else {
    $profile_img = "dist/img/favicon.png";
}

// INCLUDE HEADER (AFTER SESSION)
include "layout/head.php";

?>





<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">



        <!-- BBA -->

        <?php

        $booking_no = $_GET['booking_no'];
        $booking_id = $_GET['booking_id'];


        if (!$booking_no) {
            echo '<script> window.location="booking-list.php"; </script>';
            exit;
        }

        if (!$booking_id) {
            echo '<script>window.location="booking-list.php";</script>';
            exit;
        }


        // $query = "SELECT * FROM bba WHERE booking_no='$booking_no'";
        // $result = mysqli_query($con, $query);
        // $row = mysqli_fetch_assoc($result);





        // if (!$result || mysqli_num_rows($result) == 0) {
        //     echo "<script>alert('BBA not generated')</script>";
        //     echo "<script> window.location='booking-view.php?booking_id=$booking_id'; </script>";
        //     exit;
        // }
        ?>

                <div class="d-block text-center page-breadcrumb mb-3 pagetitle">
            <div class="my-auto">
                <div class="row">
                    <div class="col-md-10">
                        <h1>BBA</h1>
                    </div>
                    <div class="col-md-2">

                        <a href="booking-view.php?booking_id=<?= $booking_id ?>" class="btn btn-sm btn-success">
                            ← Back
                        </a>
                    </div>
                </div>

            </div>
        </div>

















<style>
    .page-break{
        page-break-after: always;
    }

/* pdf button */

.pdf-btn {
    background: #ff7d23;
    color: #fff;
    border: none;
    padding: 12px 28px;
    margin-left: 63px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    /* box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35); */
    transition: all 0.25s ease;
}


</style>





<?php
    $propertamt = $_GET['propertamt'];
?>

<form method="POST" action="generate_pdf.php">
    <input type="hidden" name="booking_no" value="<?= $booking_no ?>">
    <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
    <input type="hidden" name="propertamt" value="<?= $_GET['propertamt'] ?>">
    <button type="submit" class="pdf-btn">Download PDF</button>
</form>

<?php


// include 'pdf_content.php';

?>


<!-- BBA Docx view section only -->



<style>
    body {
        font-family: Arial, sans-serif;
    }

    .page-break {
        display: block;
        page-break-after: always;
    }

    p {
        font-size: 14px !important;
        text-align: justify;
        line-height: 1.4;
        padding: 0px 132px 0px 132px;
    }

    .pright {
        padding: 0px 132px 0px 132px;
    }


    /* table {
        width: 700px;
        border-collapse: collapse;
    } */

    table {
        width: 70% !important;
        border-collapse: collapse;
        margin-top: 10px !important;
        margin-left: 150px !important;
        margin-bottom: 40px !important;
    }

    td {
        border: 1px solid #000;
        padding: 12px;
        font-weight: bold;
    }

    td:first-child {
        width: 35%;
        background-color: #f2f2f2;
    }

    td:last-child {
        width: 65%;
    }

    @page {
        margin: 80px 40px 70px 40px;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.6;
    }

.container {
    width: 90%;
    margin-bottom: 50px;
    background-color: white;
    padding: 50px 0px;
}

    .page-break {
        page-break-after: always;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table td {
        border: 1px solid #000;
        padding: 6px;
    }

    .estamp{
     margin-bottom: 30px;
     font-weight: 700;
    }
</style>




<?php


$query = "SELECT * FROM bba WHERE booking_no='$booking_no'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);


?>








<div class="container">

    <h3 style="text-align: center; margin-top: 400px;">“GLOBAL RESIDENCY HOMES”</h3>
    <h3 style="text-align: center; margin-bottom: 135px;">PHULERA, JAIPUR, RAJASTHAN</h3>
    <p>Please read carefully..........</p>
    <p>Important Instructions to the Allottee(s)</p>
    <p>
        The Allottee(s) states and confirms that the firm has made the Allottee(s) aware of the availability
        of
        the Builder Buyers’ Agreement (hereinafter defined) at the head office of the firm. The Allottee(s)
        confirms that the Allottee(s) has read and perused the Agreement, containing the detailed terms and
        conditions and in addition, the Allottee(s) further confirms to have fully understood the terms and
        conditions of the Agreement (including the Firms limitations) and the Allottee(s) is agreeable to
        perform his obligations as per the conditions stipulated in the Agreement. Thereafter the
        Allottee(s)
        has applied for allotment of an plot/shop in the Said Complex and has requested the firm to allot a
        plot/shop. The Allottee(s) agrees and confirms to sign the Agreement in entirety and to abide by the terms and
        conditions of the Agreement and the terms and conditions, as mentioned herein.
    </p>
    
</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>

    <p>

    </p>

    <p>
        The Allottee(s) will execute two (2) copies of the Agreement for each plot/shop to be purchased. The
        Agreement sets forth in detail the terms and conditions of sale with respect to the plot//shop(s).
        The
        Allottee(s) agrees and understands that if the Allottee(s) fails to execute and deliver the
        Agreement
        along with all annexures in its original form and all amounts due and payable as per the schedule of
        payment within thirty (30) days from the date of its dispatch by the firm, then the Allottee(s)
        authorizes the firm to cancel the allotment and on such cancelation, the Allottee(s) consents and
        authorizes the firm to forfeit the Earnest Money along with Non Refundable Amounts. Thereafter the
        Allottee(s) shall be left with no right, title or interest whatsoever in the Said Plot/shop.
    </p>

    <p>
        The Allottee(s) further agrees and understands that the firm is not obliged to send any
        notice/reminders
        in this regard.
    </p>

    <p>
        The Agreement shall not be binding on the firm until executed by the firm through it’s authorized
        signatory. The firm reserves the right to request information as it may so desire concerning the
        Allottee(s). The firm will not execute any Agreement wherein the Allottee(s) has made any
        corrections/
        cancellations / alterations / modifications. The firm also has the right to reject any Agreement
        executed by any allottee(s) without any cause or explanation or without assigning any reasons
        thereof
        and the decision of the firm shall be final and binding on the Allottee(s).
    </p>

    <p>
        The Allottee(s) confirms to have read and understood the above instructions and the clauses of the
        Agreement, its annexures, etc. and the Allottee(s) now execute this Agreement and undertake to
        faithfully abide by all the terms and conditions of this Agreement.
    </p>


    <span class="pright"> <b> <?php echo $row['allottee_name']; ?> </b></span>
    <p>(Allottee(s) </p>

    <p>Instructions for execution of the Agreement:</p>

    <p>
        1) Kindly sign along with joint allottee, if any, on every page of the Agreement including all
        annexures.
    </p>

    <p>
        2) Both of the signed copies of the Agreement with all the annexures in its original form shall be
        returned to the firm by registered post (AD)/hand delivery only within the time stipulated.
    </p>

    <p>
        3) Kindly sign next to the tentative typical plot/shop plan in <b>Annexures as attached.</b>
    </p>

    <p>4) Witnesses signatures to be done only on Witness space.</p>




</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>




    <h3 style="text-align: center">“GLOBAL RESIDENCY HOMES”</h3>
    <h3 style="text-align: center">PHULERA, JAIPUR, RAJASTHAN</h3>
    <h3 style="text-align: center; margin-top: 30px;">BUILDER-BUYER AGREEMENT</h3><br>

    <p>This Agreement (the <b>‘Agreement’</b>) is made at Delhi on this <strong> <?php
    if (!empty($row['bba_date'])) {
        $date = strtotime($row['bba_date']);
        echo date("jS \\d\\a\\y \\o\\f F Y", $date);
    }
    ?></strong></p>

    <p style="text-align: center"><b>BY AND BETWEENM</b></p>

    <p>
        <b>M/s. SHAAN REALTECH PVT LTD,</b> firm presently having its Registered Office at Office -1, 2nd
        Floor,
        A-12, A-13, Sector-16, Noida, Uttar Pradesh 201301 (hereinafter referred to as the <b>‘Firm</b>
        which
        expression shall, unless it be repugnant to the context or meaning thereof, be deemed to include its
        executors, successors) acting through its Authorized Signatory of the <b>FIRST PART</b>.
    </p>

    <h4 style="text-align: center">AND</h4>

    <span class="pright">(FOR INDIVIDUALS)</span><br><br>
    <span class="pright">1st ALLOTTEE</span><br>
    <span class="pright">Mr/Ms/Mrs <strong> <?php
    echo $row['allottee_name'] . ' (Aadhar No: ' . $row['addhar_no'] . ')';
    ?></strong></span><br>
    <span class="pright">Son/Daughter/Wife of <strong> <?php
    echo $row['allottee_fname'];
    ?></strong></span><br>
    <p class="pright">R/O <strong> <?php
    echo $row['allottee_address'];
    ?></strong></p>

    <h4 style="text-align: center">AND</h4>

    <span class="pright">2nd ALLOTTEE</span><br>
    <span class="pright">Mr/Ms/Mrs ………………………………………………………………………………………………………</span><br>
    <span class="pright">Son/Daughter/Wife of………………………………………………………………………………</span><br>
    <span class="pright">R/O ……………………………………………………………………………………………</span>

    <h4 style="text-align: center">AND</h4>
    <span class="pright">3rd ALLOTTEE</span><br>
    <span class="pright">Mr/Ms/Mrs ………………………………………………………………………………………………………</span><br>
    <span class="pright">Son/Daughter/Wife of………………………………………………………………………………</span><br>
    <span class="pright">R/O ……………………………………………………………………………………………</span> <br>

</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>

    <p> <b> OR </b> </p>
    <span class="pright">(FOR FIRMS)</span><br>
    <p class="pright">M/s.________________________________________________________________a partnership/proprietorship </p> 
    <p class="pright">firm duly registered and having its office at</p>
    <p class="pright"> _____________________________________________________ through its Authorized Signatory Partner/ Sole</p>
    <p class="pright">Proprietor Mr. / Ms. / Mrs _______________________________________________________________</p>
    <p class="pright">R/o____________________________________________________________________________</p>


    <p><b> OR </b></p>

    <p>(FOR COMPANIES)</p>
    <p class="pright">M/s._____________________________________________________________________________ a firm duly</p>
    <p class="pright">registered under Companies Act, 1965 having its registered office </p> <br>
    <p class="pright"> ______________________________________________________________through its duly at Authorized Signatory Mr./Ms.</p>
    <p class="pright"> /Mrs ____________________________________________________________________ authorized by board resolution </p>
    <p class="pright"> dated _________________.</p>



    <p># (Strike out whatever is not applicable)</p>
    <p>Hereinafter jointly and severally referred to as the <b>'Allottee'</b> (which expression unless
        excluded
        by or repugnant to the context or meaning thereof, shall mean and include his/her/its heirs,
        executors,
        administrators, successors and legal representatives) of the <b>SECOND PART.</b></p>
    <p>The <b>firm </b>and <b>Allottee </b>are hereinafter individually referred to as the <b>'Party'
        </b>and
        collectively referred to as the <b>‘Parties'.</b></p>

    <p><b>Firms Representation</b></p>

    <p>
        <b>WHEREAS </b>
        the firm is bona fide purchaser of the land bearing “GLOBAL RESIDENCY HOMES”, Village Habaspura
        Tehsil
        Phulera, Dist. Jaipur, State – Rajasthan (hereinafter referred to as the 'Said Land').

    </p>

    <p>
        <b>AND WHEREAS</b>
        it is clarified that the firm has not intended to convey right or interest in any of the land
        falling
        outside the Said Building / Said Complex / Said Land and no impression of any kind has been given
        with
        regard to the constructions that may take place on the land outside the Said Land.

    </p>



</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>
    <p>
        <b>Allottee(s) Representations</b>
    </p>

    <p>
        <b>AND WHEREAS</b> the Allottee(s) vide Application Dated <strong><?php
        if (!empty($row['booking_date'])) {
            echo date("jS F Y", strtotime($row['booking_date']));
        }
        ?></strong> applied for allotment of Said Plot//shop
        (herein after defined) in the Said Complex after perusal and understanding the terms and conditions
        of
        this Agreement
    </p>


    <p>
        <b>AND WHEREAS</b> the Allottee after fully satisfying himself with the stated facts applied to the
        firm
        is in the process of developing the Residential Colony on the said Land, and in pursuance thereof,
        it is
        understood and agreed by the Allottee that the Plot//shop area and location of Plot/shop, which the
        Allottee is intending to buy are tentative and are subject to change.
    </p>


    <p>
        <b>AND WHEREAS</b> the Allottee after fully satisfying himself about the right, title, interest and
        limitation of the firm in the said land / complex has shown interest in the Complex and has
        approached
        the Firm for allotment of Plot/shop admeasuring <strong><?php
        echo $row['plot_area'];
        ?></strong> Sq. yd.vide application form dated <strong><?php
        if (!empty($row['booking_date'])) {
            echo date("jS F Y", strtotime($row['booking_date']));
        }
        ?></strong>

    </p>

    <p>
        <b>AND WHEREAS</b> the Allottee has read and understood the advance payment plans offered by SHAAN
        REALTECH PVT LTD, and hereby agree to abide by the conditions mentioned in it and the Allottee
        has/have
        chosen to pay the balance advance/subscription Agreement towards the provisional registration
        against a
        probable allotment of plot(s)/shops as per detailed in Annexure- A.
    </p>



    <p>
        <b>AND WHEREAS</b> in pursuance to the aforesaid application for allotment the firm accepted the
        application of the Allottee and allotted /shop/Plot No <strong><?php
        echo $row['plot_no'];

        ?></strong> in <b> GLOBAL RESIDENCY HOMES </b> on dated <strong><?php
        if (!empty($row['booking_date'])) {
            echo date("jS F Y", strtotime($row['booking_date']));
        }
        ?></strong> to
        the
        Allottee and the Allottee has verified and satisfied with the records which entitle the firm to
        execute
        this Agreement.
    </p>

    <p>
        <b>AND WHEREAS</b> the Allottee hereby undertakes that he/she shall abide by all laws, rules,
        regulations, notifications and terms and conditions of Rajasthan Government, as per law and any
        alteration(s)/amendment(s)/modification(s) thereto, and shall be liable for defaults and/or breaches
        of
        any of the conditions, rules or regulations as may be applicable to the said land/complex from time
        to
        time.

    </p>

    <p>
        <b>AND WHEREAS</b> the Allottee has represented and warranted to the firm that the Allottee has the
        power, competence and authority to enter into and perform this Agreement and has clearly understood
        his
        / her rights, duties, responsibilities and obligations under the Agreement.
    </p>

    <p>
        <b>AND WHEREAS</b> the firm relying on the confirmations, representations and assurances of the
        Allottee(s) to faithfully abide by all the terms, conditions and stipulations contained in this
        Agreement has accepted in good faith the Application to allot the Said Plot/shop and is now willing
        to
        enter into this Agreement on the terms and conditions appearing hereinafter.

    </p>

    <p>
        <b>NOW, THEREFORE, THIS INDENTURE WITNESSETH AND IT IS HEREBY AGREED AND DECLARED BY AND BETWEEN THE
            PARTIES HERETO AS FOLLOWS:</b>
    </p>

    <p>
        <b>'Agreement'</b> shall mean Builder Buyer’s agreement, which is executed by and between the firm
        and
        the Allottee;
    </p>
    <p>
        <b>‘Allottee’</b> means the person(s) named and referred to as party and who is being allotted the
        Said
        Plot//shop and who has signed and executed the Agreement.
    </p>


</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>

    <p>
        <b>'Date of Possession'</b> shall mean the date on which the actual physical possession is taken or
        deemed to have been taken by the Allottee
    </p>

    <p>
        <b>'Deemed Possession'</b> shall mean the possession of the Plot/shop, if not taken, by the Allottee
        possession be deemed to be delivered on the next succeeding day after the expiry date of the notice
        of
        possession;
    </p>

    <p>
        <b>"Earnest Money</b> “means 50% of the Total Price, of the Said Plot/shop payable by the
        Allottee(s)
        and more clearly setout in schedule of payments, Annexure A
    </p>

    <p>
        <b>‘External Developmental Charges (EDC)’</b> means the charges levied or livable on the Said
        Complex/
        Said Land (whatever name called or in whatever form) by the Government of Rajasthan or any other
        Governmental Authority and with all such conditions imposed to be paid by the Allottee(s) and also
        includes any further increase in such charges.
    </p>

    <p>
        <b>"Force Majeure"</b> means any event or combination of events or circumstances beyond the control
        of
        the firm which cannot (a) by the exercise of reasonable diligence, or (b) despite the adoption of
        reasonable precaution and/or alternative measures, be prevented, or caused to be prevented, and
        which
        adversely affects the Firms ability to perform obligations under this Agreement, which shall include
        but
        not be limited to:

    </p>

    <p>
        <b>(a)</b> acts of God i.e. fire, drought, flood, earthquake, epidemics, natural disasters;<br>
        <b>(b)</b> explosions or accidents, air crashes and shipwrecks, act of terrorism;<br>
        <b>(c)</b> strikes or lock outs, industrial dispute;<br>
        <b>(d)</b> non-availability of cement, steel or other construction material due to strikes of
        manufacturers,
        suppliers, transporters or other intermediaries or due to any reason whatsoever;<br>
        <b>(e)</b> war and hostilities of war, riots, bandh, act of terrorism or civil commotion;<br>
        <b>(f)</b> the promulgation of or amendment in any law, rule or regulation or the issue of any injunction,
        court order or direction from any Governmental Authority that prevents or restricts a party from
        complying with any or all the terms and conditions as agreed in this Agreement;<br>
        <b>(g)</b> any legislation, order or rule or regulation made or issued by the Govt. or any other Authority
        or
        if any Governmental Authority(ies) refuses, delays, withholds, denies the grant of necessary
        approvals
        for the Said Complex/ Said Building or if any matters, issues relating to such approvals,
        permissions,
        notices, notifications by the Governmental Authority(ies) become subject matter of any suit / writ
        before a competent court or; for any reason whatsoever;<br>
        <b>(h)</b> any event or circumstances analogous to the foregoing.
    </p>

    <p>
        <b>“IBMS”</b> means the interest bearing maintenance security to be paid by the Allottee(s) for the
        maintenance and upkeep of the Said Complex/ Said Building to be paid as per the Schedule of payments
        (attached as Annexure-B to this Agreement) to the firm
    </p>

    <p>
        <b>‘Infrastructure Development Charges (IDC)’</b> shall mean the infrastructure development charges
        levied/ leviable (by whatever name called, now or in future) by the Governmental Authority towards
        the
        cost of development of major infrastructure projects.
    </p>



</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>

    <p>
        <b> “Governmental Authority”</b> or “Governmental Authorities” shall mean any government authority,
        statutory authority, competent authority, government department, agency, commission, board, tribunal
        or
        court or other law, rule or regulation making entity having or purporting to have jurisdiction on
        behalf
        of the Republic of India or any state or other subdivision thereof or any municipality, district or
        other subdivision thereof, and any other municipal/ local authority having jurisdiction over the
        land on
        which the Said Complex/ Said Building is situated;
    </p>


    <p>
        <b>“Maintenance Agency”</b> means the Firm, its nominee(s) or association of plot/shop allottee’s or
        such other agency/ body/ Firm/ association of condominium to whom the Firm may handover the
        maintenance
        and who shall be responsible for carrying out the maintenance of the Said Complex/ Said Building.
    </p>



    <p>
        <b>“Maintenance Charges”</b> shall mean the charges payable by the Allottee(s) to the Maintenance
        Agency
        for the maintenance services of the Said Building/Said Complex, including common areas and
        facilities
        but does not include; (a) the charges for actual consumption of utilities in the Said Plot/shop
        including but not limited to electricity, water, which shall be charged based on actual consumption
        on
        monthly basis and (b) any statutory payments, taxes, with regard to the Said Plot/shop/Said
        Building/Said Complex. The details of Maintenance Charges shall be more elaborately described in the
        Maintenance Agreement.
    </p>

    <p>
        <b>“Non Refundable Amounts”</b> means the interest paid or payable on delayed payments, brokerage
        paid/payable by the Firm, if any, etc.

    <p>
        <b>“Preferential Location Charges (PLC)”</b> means charges for the preferential location
        attribute(s) of
        the Said Plot/shop payable/ as applicable to be calculated on the per sq. yd./sq. ft. based on super
        area of the Said Plot/shop, as mentioned in this Agreement.
    </p>

    <p>
        <b>“Said Plot/shop”</b> means the plot/shop allotted to the Allottee/s, details of which have been
        set
        out in clause 1 of this Agreement, the tentative typical plot/shop plan and the tentative
        specifications
        of the same given in annexure-_A_and includes any alternative plot/shop allotted in lieu of the Said
        Plot/shop.

    </p>

    <p>
        <b>“Said Complex”</b> means the <strong> “GLOBAL RESIDENCY HOMES”, HABASPURA, PHULERA, JAIPUR,
            RAJASTHAN</strong>
        comprising of residential plot/shop buildings, shops, club house swimming pool, gym etc., community
        shopping, nursery school, and any other building Amenities and Facilities as may be approved by the
        Governmental Authority.
    </p>

    <p>
        <b>“Total Price”</b> means any and all kind of the amount amongst others, payable for the Said
        Plot/shop
        which includes basic sale price, PLC (if the Said Plot/shop is preferentially located), Additional
        PLC
        calculated on per sqyd./sq.ft. based on the Area of the Said Plot/shop and EDC/IDC, but does not
        include
        other amounts, charges, security amount etc., which are payable in accordance with the terms of the
        Application/Agreement, including but not limited to : - <br>
        <b>i.</b> Wealth tax, government rates tax on land, fees or levies of all and any kinds by whatever name
        called. <br>
        <b>ii.</b> IBMS. <br>
        <b>iii.</b> Maintenance charges, property tax, municipal tax on the Said Plot/shop. <br>

    </p>
</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center> <br>
    <p>
        <b>iv.</b> Stamp duty, registration and incidental charges as well as expenses for execution of the Agreement
        and conveyance deed etc. <br>
        <b>v.</b> Taxes and Cesses. <br>
        <b>vi.</b> The cost for electric and water meter as well as charges for water and electricity connection
        and
        consumption.<br>
        <b>vii.</b>Club membership fees and club charges, as applicable.<br>
        <b>viii.</b> Escalation charges.<br>
        <b>ix.</b> Any other charges that may be payable by the Allottee(s) as per the other terms of the Agreement
        and
        such other charges as may be demanded by the Firm which amounts shall be payable by the Allottee(s)
        in
        addition to the Total Price in accordance with the terms and conditions of the Agreement and as per
        the
        demand raised by the Firm from time to time.<br>

    </p>

    <p>
        <b>Interpretation</b> <br>
        Unless the context otherwise requires in this Agreement: <br>
        <b>a.</b> the use of words importing the singular shall include plural and masculine shall include feminine
        gender and vice versa; <br>
        <b>b.</b> reference to any law shall include such law as from time to time enacted, amended, supplemented
        or
        re-enacted; <br>
        <b> c.</b> reference to the words “include” or “including” shall be construed without limitation; <br>
        <b> d.</b> reference to this Agreement, or any other agreement, deed or other instrument or document shall
        be
        construed as a reference to this Agreement or such agreement, deed or other instrument or document
        as
        the same may from time to time be amended, varied, supplemented or novated.
    </p>
    <p>
        The Allottee(s) agrees that wherever in this Agreement, it is explicitly mentioned that the Allottee(s) has
        understood or acknowledged obligations of the Allottee(s) or the rights of theFirm, the Allottee(s) has
        given consent to the actions of the Firm or the Allottee(s) has acknowledged that the Allottee(s) has no
        right of whatsoever nature, the Allottee(s) in furtherance of the same, shall do all such acts, deeds or
        things, as the Firm may deem necessary and/or execute such documents/deeds in favour of the Firm at
        the first request without any protest or demur.


    </p>

    <p>
        <b>1.</b> That the Firm hereby agrees to sell/ convey/ Transfer the /shop/Plot NO. <strong><?php
        echo $row['plot_no'];

        ?> </strong> admeasuring <strong><?php
         echo $row['plot_area'];
         ?></strong>
        SqYd
        in favour of Allottee, Khasra no. <strong><?php
        echo $row['khasra_no'];
        ?></strong> at Village Habaspura, Tehsil Phulera, Dist. Jaipur, State –
        Rajasthan.
    </p>

    <p>
        <b>2.</b> The Allottee(s) has paid a sum of Rs.
        <strong><?php echo $row['total_amount'] . "/- (RUPEES"; ?></strong> /- (
        <strong><?= $row['total_amount_word'] ?></strong>) being part payment towards the Total Price
        at
        the time of Application, the receipt of which the Firm both hereby acknowledge and the Allottee(s)
        agrees to pay the remaining price of the Plot//shop as prescribed in schedule of payments
        (annexure-A__)
        attached with this Agreement along with all other charges, Taxes and Cesses, securities, etc. as may
        be
        demanded by the Firm within the time and in the manner specified therein.

    </p>





</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>


    <p>
        <b>3.</b> The Allottee(s) agrees and confirms that out of the total amount(s) paid/payable by the Allottee(s)
        for the Said Plot/shop, 50% of the Total Price of the Said Plot/shop shall be treated as Earnest
        Money to ensure fulfillment of the terms and conditions as contained in the Application and this
        Agreement. In the event, the Allottee(s) fails to perform any obligations or commit breach of any
        of the terms and conditions, mentioned in the Application and/or this Agreement, including but
        not limited to the occurrence of any event of default as stated in this Agreement and the failure
        of the Allottee(s) to sign and return this Agreement in original to the Firm within 30 days of
        dispatch, the Allottee(s) agrees, consents and authorizes the Firm to cancel the allotment and on
        such cancellation, the Allottee(s) authorizes the Firm to forfeit the Earnest Money along with Non
        Refundable Amounts. Thereafter the Allottee(s) shall be left with no right, interest and lien on the
        Said Plot/shop/Said Complex. This is in addition to any other remedy/right, which the Firm may
        have. If the amount paid by the Allottee(s) is less than the forfeitable amount, then the Allottee(s)
        undertakes to make good the shortfall of the forfeitable amounts.
    </p>
    <p>
        <b>4.</b> The Allottee understands that the part advance given by him/them is towards provisional
        registration
        against to probable’s allotments of plot(s)/shop(s)/in the ongoing project. That further Understand
        the
        Allotee at the provisional registration against a probable’s allotment is subject to the following
        conditions: <br> <br> <br>

        <b>I.</b> EDC And IDC shall be charged extra @ 150/Sq. yard for plots and @ 50 /Sq. ft. for shop. <br>
        <b>II.</b> PLC(s) determined by the firm shall be charged extra and will be charged proportionally with
        every
        Advances payment installments. There will be three types of PLC applicable on both plots and Shops
        (1)
        Corner, (2) Wide Road, (3) park facing, (4) facility. Payment for PLC’s will be as Follows: one
        PLC’s-
        6% of BSP, Two PLC’s- 9% of BSP, Three PLC’s 12% of BSP.
    </p>
    <p> <b>III.</b> All other charges like maintenance deposits and such other charges as may be determined by the
        firm
        at the time of allotment/possession, shall be charged extra and compulsory to initiate final
        registration process. <br>
        <b>IV.</b> Registration charge, stamp duty and service tax will be extra as per the applicable rate.<br>
        <b>V.</b> The all PDC’s towards part advance payment installments for provisional registration must be honored.
        In the first instance. In the case any PDC is dishonored, firm reserves the right to cancel the
        provisional registration without any notice. it is further understood that, without any prejudice to
        firms any right in case of the dishonor of Allottee part advance payment cheque, firm may at its own
        discretion are paid by the Allottee along with simple interest @ 18% p.a in addition to cheque
        bouncing
        charge of Rs 1500/ instance and cheque collection charge of Rs 1500/ Instance within 7 days from the
        date of cheque bouncing.<br>
        <b>VI.</b> Any variation in the total sale consideration, due to change in EDC, infrastructure development
        charges or any other charges so demanded by the state government and /or authorities or any other
        government department, the Agreement as apportioned by the firm shall be final and bindings on
        Allottee.<br>

        The Allottee(s) shall make all payments within the stipulated time as mentioned in the schedule of
        payments as given in Annexure-A annexed to this Agreement and other charges and amounts, as may be
        demanded by the Firm from time to time, without any reminders from the Firm, through A/c payee
        cheque(s)/ demand draft(s) in favour of ‘_SHAAN REALTECH PVT LTD_' or transfer online to:

    </p>





</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>

    <table>
        <tr>
            <td>Account Name:</td>
            <td>SHAAN REALTECH PVT LTD</td>
        </tr>
        <tr>
            <td>Account Number:</td>
            <td>03278630000188</td>
        </tr>
        <tr>
            <td>Bank:</td>
            <td>HDFC BANK LTD</td>
        </tr>
        <tr>
            <td>Branch:</td>
            <td>PASCHIM VIHAR , Delhi-110087,</td>
        </tr>
        <tr>
            <td>IFSC CODE:</td>
            <td>HDFC0000327</td>
        </tr>
    </table>

    <p>
        <b> 5. </b>The Allottee understands that the project is still at the concept stage and decision and
        developments
        will to an extent depend on the kind of Allottee response as generated by this and like request
        besides
        the completions of land acquisition, conversion and approval of plans.

    </p>
    <p>
        <b>6.</b> The Allottee further understands that the Agreement paid hereby and through the provisional
        registration against a probable’s allotment shall be converted into allotment only upon the
        intimations
        by the firm post sanctions of the plans provided all payments due have been paid timely.
    </p>

    <p>
        <b>7.</b> The Allottee understands that the allotment shall be subject to due execution of the firms
        Agreement
        in its standard format including maintenance agreement IBMS as per Annexure B and acceptance by him/
        them of all term and condition of the firm.
    </p>

    <p>
        <b>8.</b> A. The Allottee understands that there is a lock in period of 24 months from the date of the
        realization of first part advance payment with a grace period of 6 months in order to claim 18%
        interest
        for delay on possession on the paid amount unless there shall be delay or failure due to Force
        Majeure
        conditions including but not limited to failure of the Allottee(s) to pay in time the Total Price
        and
        other charges and dues/payments mentioned in this Agreement or any failure on the part of the
        Allottee(s) to abide by all or any of the terms and conditions of this Agreement during which the
        Allottee understands that he /she will not be entitled to any refund of the money from the firm.

    </p>

    <p>
        B. Possession will be given within lock in period of 24 months along with 6 months grace period. If
        the
        possession of the Said Plot/shop is delayed due to Force Majeure conditions, then the Firm shall be
        entitled to extension of time for delivery of possession of the Said Plot/shop. The Firm during the
        continuance of the Force Majeure, reserves the right to alter or vary the terms and conditions of
        this
        Agreement or if the circumstances so warrant, the Firm may also suspend the development of the
        project
        for such period as is considered expedient, the Allottee(s) agrees and consents that the Allottee(s)
        shall have no right to raise any claim, compensation of any nature whatsoever for or with regard to
        such
        suspension. The Allottee(s) agrees and understands that if the Force Majeure condition continues for
        a
        long period, then the Firm alone in its own judgment and discretion, may terminate this Agreement
        and in
        such case the only liability of the Firm shall be to refund the amounts without any interest or
        compensation whatsoever. The Allottee(s) agrees that the Allottee(s) shall have no right or claim of
        any
        nature whatsoever and the Firm shall be released and discharged of all its obligations and
        liabilities
        under this Agreement.
    </p>

    <p>
        C. The builder will provide basic facilities like internal Roads, Parks, External Boundary Walls,
        Street
        Lights, Security System, etc…..
    </p>


</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>


    <p>
        D. If the Firm is unable to construct/continue or complete the construction of the Said
        Building/Said
        Complex due to Force Majeure conditions or due to any government/regulatory authority’s action,
        inaction
        or omission, then the Firm may challenge the same by moving the appropriate courts, tribunal(s) and
        / or
        authority. In such a situation, the amount(s) paid by the Allottee(s) shall continue to remain with
        the
        Firm and the Allottee(s) shall not have a right to terminate this Agreement and ask for refund of
        his
        money and this Agreement shall remain in abeyance till final determination by the court(s) /
        tribunal(s)
        / authority (ies). However, the Allottee(s) may, if so desires become a party along with the Firm in
        such litigation to protect Allottee’s rights arising under this Agreement. In the event the Firm
        succeeding in its challenge to the impugned legislation or rule, regulation, order or notification
        as
        the case may be, it is hereby agreed that this Agreement shall stand revived and the Allottee(s)
        shall
        be liable to fulfill all obligations as provided in this Agreement. It is further agreed that in the
        event of the aforesaid challenge of the Firm to the impugned legislation, order, rules, regulations,
        notifications, and the said legislation, order, rules, regulations, notifications become final,
        absolute
        and binding, the Firm will, subject to provisions of law/court order, refund within reasonable time
        to
        the Allottee(s) the
    </p>

    <p>
        amounts received from the Allottee(s) after deducting Non Refundable Amounts,
        but without any interest or compensation and the decision of the Firm in this regard shall be final and
        binding on the Allottee(s) save as otherwise provided herein, the Allottee(s) shall be left with no
        other right, claim of whatsoever nature against the Firm under or in relation to this Agreement.
    </p>

    <p>
        <b>9.</b> The Allottee(s) authorizes the Firm to adjust/appropriate all payments that shall be made by the
        Allottee(s) under any head(s) of dues against outstanding heads in Allottee’s name and the
        Allottee(s)
        shall not have a right to object/demand/direct the Firm to adjust the payments in any manner
        otherwise
        than as decided by the Firm.
    </p>

    <p>
        <b>10.</b> The Allottee(s) agrees that time is essence with respect to payment of Total Price and other
        charges, deposits and amounts payable by the Allottee(s) as per this Agreement and/or as demanded by
        the
        Firm from time to time and also to perform/observe all the other obligations of the Allottee(s)
        under
        this Agreement. The Firm is not under any obligation to send any reminders for the payments to be
        made
        by the Allottee(s) as per the schedule of payments and for the payments to be made as per demand by
        the
        Firm or other If any delay in due payment then the firm will charge 18% p.a interest on pro-rata
        basis
        and if such delay continue for 90 days then allotment will automatically get transfer to Market
        Payment
        Plan.
    </p>

    <p>
        <b>11.</b> If any delay in due payment, then the firm will charge 18% p.a interest on pro-rata basis and if
        such delay continue for 90 days then allotment will automatically get transfer to Market Payment
        Plan.
    </p>
    <p>
        <b> 12.</b> The Allottee(s) has seen and accepted the schedule of payments, (as given in Annexure-A) .The
        Firm
        may in its sole discretion or as may be directed by any Governmental Authority (ies) or due to Force
        Majeure conditions carry out, such additions, alterations, deletions and/ or modifications in the
        plot//shop floor plans, specifications, etc., including but not limited to change in the position of
        the
        Said Plot//shop, change in the number of Said Plot//shop, change in the area and/ or change in the
        dimension of the Said Plot//shop at any time thereafter till the grant of Conveyance Deed/Registry.
        The
        Allottee(s) agrees and understands that the construction will commence only after all necessary
        approvals are received from the concerned authorities.
    </p>

    <p>
        <b>13.</b> In case of any alteration/modifications resulting in (+)(-)10% change in the plot Area of the
        Said
        Plot/shop any time prior to and upon the grant of intimation letter/Conveyance Deed/ registration,
        the
    </p>











</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>


    <p>
        Firm shall intimate in writing to the Allottee(s) the changes thereof and the resultant change, if
        any,
        in the Total Price of the Said Plot/shop to be paid by the Allottee(s) and the Allottee(s) agrees to
        deliver to the Firm written consent or objections to the changes within thirty (30) days from the
        date
        of dispatch by the Firm. In case the Allottee(s) does not send his written consent, the Allottee(s)
        shall be deemed to have given unconditional consent to all such alterations/modifications and for
        payments, if any, to be paid in consequence thereof. If the Allottee(s) objects in writing
        indicating
        his non-consent/objections to such alterations/modifications then in such case alone the Firm may at
        its
        sole discretion decide to cancel this Agreement without further notice and refund the entire money
        received from the Allottee(s) in six equal installments within ninety (90) days from the date of
        intimation received by the Firm from the Allottee(s). Upon the decision of the Firm to cancel the
        Said
        Plot/shop, the Firm shall be discharged from all its obligations and liabilities under this
        Agreement
        and the Allottee(s) shall have no right, interest or claim of any nature whatsoever on the Said
        Plot/shop.
    </p>
    <p>
        <b>14.</b> The Firm, upon obtaining necessary approvals from the Govt authority shall offer in writing
        possession of the Said Plot/shop to the Allottee(s). Within 30 days from the date of issue of such
        notice and the Firm shall give possession of the Said Plot/shop to the Allottee(s) provided the
        Allottee(s) is not in default of any of the terms and conditions of this Agreement and has complied
        with
        all provisions, formalities, documentation, etc., as may be prescribed by the Firm in this regard.
        The Allottee(s) shall be liable to pay the Maintenance Charges from the date of grant of the
        intimation
        letter or Conveyance deed whichever is earlier irrespective of the date on which the Allottee(s)
        takes
        possession of the Said Plot/shop.

    </p>

    <p>
        <b>15.</b> Upon receiving a written intimation from the Firm in terms of clause 13 above, the Allottee(s)
        shall
        within the time stipulated by the Firm, take possession of the Said Plot//shop from the Firm by
        executing necessary indemnities, undertakings, and such other documentation as the Firm may
        prescribe
        and by making all the payments to the Firm of all charges/dues as specified in this Agreement and
        the
        Firm shall after satisfactory execution of such documents give possession of the Said Plot/shop to
        the
        Allottee(s), provided the Allottee(s) is not in breach of any other term of this Agreement. If the
        Allottee(s) fails to take the possession of the Said Plot/shop as aforesaid within the time limit
        prescribed by the Firm in its notice, then the Said Plot/shop shall be at the risk and cost of the
        Allottee(s) and the Firm shall have no liability or concern thereof. Further it is agreed by the
        Allottee(s) that in the event of the Allottee’s failure to take possession of the Said Plot/shop in
        the
        manner as aforesaid, the Firm shall `have the option to cancel this Agreement and avail the remedies
        as
        are available in Law including as stipulated in clause 28 of this Agreement or the Firm may, without
        prejudice to its rights under any of the clauses of this Agreement and at its sole discretion,
        decide to
        condone the delay by the Allottee(s) in taking possession of the Said Plot//shop in the manner as
        stated
        in this clause on the condition that the Allottee(s) shall pay to the Firm holding charges @ Rs30/-
        per
        sq. yd/month. of the Plot Area per month for any delay of full one month or any part thereof in
        taking
        possession of the Said Plot//shop for the entire period of delay. The Allottee(s)acknowledges that
        the
        charges stipulated above are just, fair and reasonable which the Firm will suffer on account of
        delay in
        taking possession of the Said Plot//shop by the Allottee(s) That on such condition and after
        receiving
        entire amount of charges together with all other amounts due and payable under this Agreement (along
        with due interest, if any, thereon) the Firm shall hand over the possession of the Said Plot/shop to
        the
        Allottee(s).For the avoidance of any doubt it is clarified that these charges are in addition to
        maintenance or any other charges as provided in this Agreement. Further, the Allottee(s) agrees that
        in
        the event of the Allottee’s failure to take possession of the Said Plot/shop within the time
        stipulated
        by the Firm in its notice, the Allottee(s) shall have no right or claim in respect of any item of
        work
        in the Said Plot//shop which the Allottee(s) may allege not to have been carried out or completed or
        in
        respect of any design specifications, building materials or any other reason whatsoever and the
        Allottee(s) shall be deemed to have been fully satisfied in all respects concerning construction and
        all
        other work relating to the Said Plot/shop/Said Building/Said Complex.
    </p>

    <p>
        <b>16.</b> If for any reasons other than those given in clauses 8(b), 8(c) and clause 28, the Firm is
        unable to
        or fails to deliver possession of the Said Plot//shop to the Allottee(s) within Twenty Four(24)
        months
        with a grace period of 6 months from the date of Application or within any extended period or
        periods as
        envisaged under this Agreement, then in such case, the Allottee(s) shall be entitled to give notice
        to
        theFirm, within ninety (90) days from the expiry of said period of Twelve (12) months with a grace
        period of 6 months or such extended periods, as the case may be, for terminating this Agreement. In
        that
        event, the Firm shall be at liberty to sell and/or dispose of the Said Plot//shop and the Parking
        Space(s) to any other party at such price and upon such terms and conditions, as the Firm may deem
        fit
        and thereafter the Firm shall within ninety (90) days from the date of full realisation of the sale
        price after sale of Said Plot//shop refund to the Allottee(s),in six equal installments, without any
        interest, the amounts paid by the Allottee(s) in respect of the Said Plot//shop without deduction of
        Earnest Money but after deduction of brokerage paid by the Firm to the broker / sales organizer in
        case
        the booking is done through a broker/sales organizer. For the avoidance of doubt, it is stated that
        the
        Allottee(s)shall have no other right or claim against the Firm in respect of the Said Plot/shop and
        Parking Space(s).If the Allottee(s) fails to exercise the right of termination within the time limit
        as
        aforesaid, then the Allottee’s right to terminate this Agreement shall stand waived off and the
        Allottee(s) shall continue to be bound by the provisions of this Agreement.
    </p>

    <p>
        <b>17.</b> Subject to the terms and conditions of the Agreement, in case of any delay other than clause 28
        and
        conditions as mentioned in clause 8(b) and 8(c) by the Firm incompletion of handing over possession
        of
        the Said Plot/shop, the Firm shall pay compensation @ Rs. 20 per sq. Yd/ft.of the Super Area of the
        Said
        Plot/shop per month or any part thereof only to the first named Allottee(s) and not to anyone else.
        The
        Allottee(s) agrees and confirms that the compensation herein is a just and equitable estimate of the
        damages which the Allottee(s) may suffer and theAllottee(s) agrees that it shall have no other right
        claims whatsoever. The adjustment of such compensation shall be done only at the time of execution
        of
        conveyance deed of the SaidPlot/shop to the Allottee(s) first named.
    </p>

    <p>
        <b>18.</b> The Firm, its associates/subsidiaries shall execute a Conveyance Deed to convey the title, of
        the
        Said Plot/shop in favour of the Allottee(s), provided the Allottee(s) has paid the Total Price and
        other
        charges in accordance with this Agreement and the Allottee(s) is not in breach of all or any of the
        terms of this Agreement.
    </p>

    <p>
        <b>19.</b> In order to provide necessary maintenance services, upon the completion of the Said
        Building/Said
        Complex the maintenance of the Said Building / Said Complex may be handed over to the Maintenance
        Agency. The Allottee(s) agrees to execute Maintenance Agreement (draft given in annexure-B__ to this
        Agreement) with the Maintenance Agency or any other nominee/agency or other body/association of
        plot//shop owners as may be appointed by the Firm from time to time for the maintenance and upkeep
        of
        the Said Building/ Said ComplexThe Allottee(s) further undertakes to abide by the terms and
        conditions
        of the Maintenance Agreement and to pay promptly all the demands, bills, charges as may be raised by
        the
        Maintenance Agency from time to time. the Firm reserves the right to change, modify, amend, impose
        additional conditions in the Maintenance Agreement at the time of its final execution.
    </p>
</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>


    <p>
        The MaintenanceCharges shall become applicable/payable from the date of intimation letter or the date of
        Conveyance deed whichever is earlier. It is further specifically clarified that the draft Maintenance Agreement,
        set out in annexure-_B_ to this Agreement is merely an indicative Maintenance Agreement that is
        proposed
        to be entered into with the Allottee(s) for maintenance and upkeep of the Said Building / Said
        Complex,
        however, if at any time, after having taken over the Said Building / Said Complex, the Maintenance
        Agency, said association of plot//shop owners/ condominium of association decides to modify, alter,
        add,
        delete any one or more of the terms and conditions of the Maintenance Agreement, the Allottee(s)
        shall
        not have any objection to the same and shall execute the Maintenance Agreement as may be required by
        the
        Maintenance Agency or association of plot//shop owners or association of condominium or its nominees
        or
        assigns.
    </p>

     <p>
        <b>20.</b> The total Maintenance Charges shall be more elaborately described in the Maintenance Agreement
        (draft given in annexure -_B_). The Allottee(s) undertakes to pay the same promptly. It is agreed by
        the
        Allottee(s) that the payment of Maintenance Charges will be applicable whether or not the possession
        of
        Said Plot//shop is taken by the Allottee(s). The Maintenance Charges shall be recovered on such
        estimated basis which may also include the overhead cost on monthly / quarterly intervals as may be
        decided by the Maintenance Agency and adjusted against the actual audited expenses as determined at
        every end of the financial year and any surplus/deficit thereof shall be carried forward and
        adjusted in
        the maintenance bills of the subsequent financial year. The estimates of the Maintenance Agency
        shall be
        final and binding on the Allottee(s). The Allottee(s) agrees and undertakes to pay the maintenance
        bills
        on or before due date as intimated by the Maintenance Agency.
    </p>

    <p>
        <b>21.</b> The Allottee(s) shall not use the Said Plot//shop for any purpose other than for residential
        purpose
        or commercial use, as prescribed; or use the same in a manner that may cause nuisance or annoyance
        to
        other plot/shop owners or residents of the Said Complex; or for any commercial or illegal or immoral
        purpose; or to do or cause anything to be done in or around the Said Plot//shop which tends to cause
        interference to any adjacent plot(s) / building(s) or in any manner interfere with the use of roads
        or
        amenities available for common use. The Allottee(s) shall indemnify the Firm against any action,
        damages
        or loss due to misuse for which the Allottee(s) / occupant shall be solely responsible.
    </p>

    <p>
        <b>22.</b> (a). The Allottee(s) agrees and understands that terms and conditions of the Agreement may be
        modified/amended by the Firm in accordance with any directions/order of any court of law,
        Governmental
        Authority, in compliance with applicable law and such amendment shall be binding on the Allottee(s).
    </p>
    <p>
        (b). The Allottee(s) further agrees that the Maintenance Schedule (annexure-_B_) attached to this
        Agreement is annexed to acquaint the Allottee(s) with the terms and conditions as may be stipulated
        as
        and when it is finally executed at the appropriate time to be notified by the Firm. The Allottee(s)
        consents to the terms and conditions contained in the draft which shall substantially be the same in
        the
        final document to be executed at the appropriate time to be notified by the Firm. The Allottee(s)
        further understands that the Firm shall have the right to impose additional terms and conditions or
        to
        modify/amend/change the terms and conditions as stated in this draft in the final document to be
        executed at the appropriate time. 
    </p>





</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>


   <p>The Firm further reserves the right to correct, modify, amend or
        change all the annexures attached to this Agreement and also annexures which are indicated to be
        tentative at any time prior to the execution of the Conveyance Deed/intimation letter of the Said
        Plot//shop.</p>

    
    <p>
        <b>23.</b> The Allottee(s) agrees that the provisions of this Agreement, Maintenance Agreement, and those
        contained in other annexures are specific and applicable to plot//shops offered for sale in the Said
        Complex and these provisions cannot be read in evidence or interpreted in any manner in or for the
        purpose of any suit or proceedings before any Court(s), Commission, Consumer Disputes Forum(s) or
        any
        other judicial forum involving any other plot//shop(s)/building(s)/project(s) of the Firm/ its
        associates/subsidiaries, partnership firms in which the Firm is partner or interested.
    </p>

    <p>
        <b>24.</b> The Allottee(s) agrees and understands that if any provision of this Agreement is determined to
        be
        void or unenforceable under applicable law, such provisions shall be deemed amended or deleted in so
        far
        as reasonably inconsistent with the purpose of this Agreement and to the extent necessary to conform
        to
        applicable law and the remaining provisions of this Agreement shall remain valid and enforceable as
        applicable at the time of execution of this Agreement.
    </p>

    <p>
        <b>25.</b> The Firm shall not be responsible or liable for not performing any of its obligations or
        undertakings provided for in this Agreement if such performance is prevented due to Force Majeure
        conditions.
    </p>

    <p>
        <b> 26.</b> The execution of this Agreement will be complete only upon its execution by the Firm through its
        authorized signatory at the Firms head office at, Office -1, 2nd Floor, A-12, A-13, Sector-16,
        Noida,
        Uttar Pradesh 201301, after the copies are duly executed by the Allottee(s) and are received by the
        Firm
        .
    </p>

    <p>
        <b>27.</b> All notices are to be served on the Allottee(s) as contemplated in this Agreement shall be
        deemed to
        have been duly served if sent to the Allottee(s) or the Firm by registered post at their respective
        addresses specified below:
    </p>

    <p>
        <b>R/O <?php
        echo $row['allottee_address'];
        ?></b>
    </p>

    <p>
        It shall be the duty of the Allottee(s) to inform the Firm of any change subsequent to the execution
        of
        this Agreement in the above address by Registered Post failing which all communications and letters
        posted at the above address shall be deemed to have been received by the Allottee(s).
    </p>

        <p>
        <b>28.</b> The Allottee(s) agrees that all defaults, breaches and/or non-compliance of any of the terms and
        conditions of this Agreement shall be deemed to be events of defaults liable for consequences
        stipulated
        herein. Some of the indicative events of defaults are mentioned below which are merely illustrative
        and
        are not exhaustive.
    </p>

    <p>
        <b>I.</b> Failure to make payments within the time as stipulated in the schedule of payments as given in
        annexure-A_and failure to pay the stamp duty, legal, registration, any incidental charges, any
        increases
        in security including but not limited to IBMS as demanded by the Firm , any other charges, deposits
        for
        bulk supply of electrical energy, Taxes etc. as may be notified by the Firm to the Allottee(s) under
        the
        terms of this Agreement, and all other defaults of similar nature.

    </p>




</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>

    



    <p>
        <b>II.</b> Failure to take possession of the Said Plot//shop within the time stipulated by the Firm .
    </p>

    <p>
        <b>III.</b> Failure to execute Maintenance Agreement and/or to pay on or before its due date the
        Maintenance
        Charges, maintenance security deposits, deposits/charges for bulk supply of electrical energy or any
        increases in respect thereof, as demanded by the Firm , its nominee, other Body or Association of
        Plot/shop Owners/Association of Condominium, as the case may be.

    </p>


    <p>
        <b>IV.</b> Assignment of this Agreement or any interest of the Allottee(s) in this Agreement without prior
        written consent of the Firm .
    </p>
    <p>
        <b>V.</b> Dishonour of any cheque(s) given by the Allottee(s) for any reason whatsoever.
    </p>

    <p>
        <b>VI.</b> Escalation Charges
    </p>
    <p>
        <b>VII.</b> Any other acts, deeds or things which the Allottee(s) may commit, omit or fail to perform in
        terms
        of this Agreement, any other undertaking, affidavit/Agreement/indemnity etc. or as demanded by the
        Firm
        which in the opinion of the Firm amounts to an event of default and the Allottee(s) agrees and
        confirms
        that the decision of the Firm in this regard shall be final and binding on the Allottee(s).
    </p>

    <p>
        Unless otherwise provided in this Agreement, upon the occurrence of any one or more of event(s) of
        default under this Agreement including but not limited to those specified above, the Firm may, in
        its
        sole discretion, by notice to the Allottee(s), cancel this Agreement by giving in writing thirty
        (30)
        days from the date of issue of notice to rectify the default as specified in that notice. In default
        of
        the above, this Agreement shall stand cancelled without any further notice. If the default is not
        rectified within such thirty (30)days, this Agreement shall stand cancelled without any further
        notice
        or intimation and the Firm shall have the right to retain Earnest Money along with the interest on
        delayed payments, any interest paid, due or payable, any other amount of a non-refundable nature.
        The
        Allottee(s) acknowledges that upon such cancellation of this Agreement, the Allottee(s) shall have
        no
        right or interest on the Said Plot//shop and the Firm shall be discharged of all liabilities and
        obligations under this Agreement and the Firm shall have the right to sell or deal with the Said
        Plot//shop in the manner in which it may deem fit as if this Agreement had never been executed.
        There
        fund, if any, shall be refunded by the Firm by registered post only after realizing amount on
        further
        sale/resale to any other party and without any interest or compensation whatsoever to the
        Allottee(s).

    </p>

    <p>
        This will be without prejudice to any other remedies and rights of the Firm to claim other
        liquidated
        damages which the Firm might have suffered due to such breach committed by the Allottee(s).
    </p>
    <p>
        <b>29.</b> All or any disputes arising out or touching upon or in relation to the terms this Builder Buyer
        Agreement including the interpretation and validity of the terms thereof and the respective rights
        and
        obligations of the parties, which cannot be amicably settled, shall be settled through arbitration.
        The
        arbitration proceedings shall be governed by the Arbitration and Conciliation Act, 1996 or any
        statutory
        amendments / modifications thereof for the time being in force. The arbitration proceedings shall be
        held by a sole arbitrator who shall be appointed by the Managing Director of the Firm. The Allottee
        hereby confirms that he / she shall have no objection in this appointment. In case of any
        proceeding,
        reference etc. touching upon the arbitration subject including any award, the territorial
        jurisdiction
        of the competent courts of Rajasthan.
    </p>

</div>
<div class="page-break"></div>
<div class="container">
    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>

    <p>
        <b> 30.</b> That no Allottee shall have any rights to invoke jurisdiction of Civil Court directly without
        availing remedy of Arbitration.
    </p>

    <p>
        <b>31.</b> You are requested to keep one copy of this Agreement with you and return the second copy to us
        duly
        signed within 7 days failing of which we will presume that the given terms & conditions of this
        Agreement are acceptable to you.
    </p>

    <p>
        <b>32.</b> This provisional agreement shall be null and void after final registration of plot/plots.

    </p>
    <p>
        For & on Behalf of
    </p><br><br><br>

    <p>
        <b>SHAAN REALTECH PVT LTD</b><br> 
        (Authorized signatory)
    <p><br><br><br>

    <p>
        IN WITNESS WHEREOF the parties hereto have hereunto and to a duplicate copy hereof set and
        subscribed
        their respective hands at the places and on the day, month and year mentioned under their respective
        signatures:
    </p>

    <p>
        SIGNED AND DELIVERED BY THE WITHIN NAMED Allottee: (including joint Allottees)<br>
        (1)__________________________________________<br>
        (2)__________________________________________<br>
        at ________________ on ___________________ in the presence of:
    </p>


    <p>
        <b>WITNESSES:</b><br>
        1. Signature ___________________________________<br>
        Name_______________________________________<br>
        Address_____________________________________<br>
        (to be completed by the Allottee(s)

    </p>

    <p>
        2. Signature ___________________________________<br>
        Name_______________________________________<br>
        Address_____________________________________

    </p>

    <p>
        SIGNED AND DELIVERED by the within named Firm at _______ on<br>
        ____________________in the presence of:

    </p>

    <p>
        ___________________________<br>
        (AUTHORISED SIGNATORY)

    </p>





</div>
<div class="page-break"></div>
<div class="container">
    <center><u> <?php echo $row['estamp_no']; ?></u> </center>

    

        <p>
        <b>WITNESSES:</b><br>
        1. Signature ___________________________________<br>
        Name_______________________________________<br>
        Address_____________________________________
        ____________________________________________

    </p>

    <p>
        <b>FOR AND ON BEHALF</b><br>
        2. Signature ___________________________________<br>
        Name_______________________________________<br>
        Address_____________________________________
        ____________________________________________

    </p>



</div>
<div class="page-break"></div>
<div class="container">

    <center class="estamp"><u> <?php echo $row['estamp_no']; ?></u> </center>


    <style>
        /* Table overall */
        #example1 {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* important for wrapping */
        }

        /* Table headers & cells */
        #example1 th,
        #example1 td {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 13px;
            /* text-align: left; */
            vertical-align: top;

            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Optional: header styling */
        #example1 th {
            background: #f5f5f5;
            font-weight: 600;
        }
    </style>

    <table id="example1" class="table table-bordered table-striped">
        <tr>
            <th style="width:70px;">Booking Date</th>
            <th style="width:90px;">Client Name</th>
            <th style="width:80px;">Allotted Unit</th>
            <th style="width:80px;">Area (Sq. Yds.)</th>
            <th style="width:90px;">Payment Plan</th>
            <th style="width:110px;">Basic Sales Price (Per Sq. Yard)</th>
            <th style="width:60px;">PLC</th>
            <th style="width:60px;">IDC</th>
            <th style="width:90px;">Total Cost</th>
        </tr>

        <?php

        $query = "SELECT * FROM booking_master WHERE booking_id='$booking_id'";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        ?>

        <tr>
            <td><?php echo $row['booking_date']; ?></td>
            <td><?php echo $row['booking_fname'] . ' ' . $row['booking_lname']; ?></td>
            <td><?php echo $row['booking_plotno']; ?></td>
            <td><?php echo $row['booking_plotarea']; ?></td>
            <td><?php echo $row['booking_payplan']; ?></td>
            <td><?php echo $row['booking_plotrate']; ?></td>
            <td><?php echo $row['booking_plc']; ?></td>
            <td><?php echo $row['booking_idc']; ?></td>
            <td><?php echo $row['booking_totalamt']; ?></td>
        </tr>
    </table>





    <style>
        /* Table overall */
        #example1 {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* important for wrapping */
        }

        /* Table headers & cells */
        #example1 th,
        #example1 td {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 13px;
            /* text-align: left; */
            vertical-align: top;

            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Optional: header styling */
        #example1 th {
            background: #f5f5f5;
            font-weight: 600;
        }
    </style>

    <div class="table-responsive">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style=" text-align:center; ">SNO</th>
                    <th style=" text-align:center; ">Installment Date</th>
                    <th style=" text-align:center; ">Particulars</th>
                    <th style=" text-align:center; ">%</th>
                    <th style=" text-align:center; ">Amount</th>
                    <th style=" text-align:center; ">Remaining Amount</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $sn = 1;
                $cumulative_amount = 0;
                $totalamt = $propertamt;
                $installmentqry = "SELECT * FROM `installment_master` WHERE installment_bookingid='$booking_id' ORDER BY installment_date";
                $installmentres = mysqli_query($con, $installmentqry);
                if (!$installmentres) {
                    error_log("Failed to fetch installments: " . mysqli_error($con));
                }
                while ($installmentdata = mysqli_fetch_assoc($installmentres)) {
                    $cumulative_amount += $installmentdata['installment_amount'];
                    $remaining_amount = $totalamt - $cumulative_amount;
                    ?>
                    <tr>
                        <td style=" text-align:center; "><?php echo $sn; ?></td>
                        <td style=" text-align:center; ">
                            <?php echo date('d/m/Y', strtotime($installmentdata['installment_date'])); ?>
                        </td>
                        <td style=" text-align:center; ">
                            <?php echo htmlspecialchars($installmentdata['installment_particular']); ?>
                        </td>
                        <td style=" text-align:center; ">
                            <?php echo htmlspecialchars($installmentdata['installment_emiper']); ?>
                        </td>
                        <td style=" text-align:center; ">
                            <?php echo number_format($installmentdata['installment_amount'], 2); ?>
                        </td>
                        <td style=" text-align:center; ">
                            <?php echo number_format($remaining_amount, 2); ?>
                        </td>
                        <!-- <td><?php echo number_format($remaining_amount, 2); ?></td> -->
                    </tr>
                    <?php
                    $sn++;
                }
                ?>
            </tbody>
        </table>
    </div>

</div>



<!-- BBA Docx view section only -->


















    </div>
</div>




<?php
include "layout/footer.php";
?>