<?php
include "database.php";

$paymentslipnoare = $_POST['booking_no'];
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>SNO</th>
                <th>Advisor Name</th>
                <th>Received Amt</th>
                <th>Receive Date</th>
                <th>Method</th>
                <th>Notes</th>
                <th>Delete</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $asn = 0;
        $adpmtque = "SELECT * FROM advisor_payments WHERE booking_no='$paymentslipnoare' ORDER BY id ASC";

        $res = mysqli_query($con, $adpmtque);

        while ($row = mysqli_fetch_assoc($res)) {
            $asn++;
        ?>
            <tr>
                <td><?= $asn ?></td>
                <td><?= $row['advisor_name'] ?></td>
                <td><?= $row['advisor_receive_amt'] ?></td>
                <td><?= $row['receive_date'] ?></td>
                <td>
                    <?= $row['method'] ?>
                    <?php if (!empty($row['other_method'])): ?>
                        (<?= $row['other_method'] ?>)
                    <?php endif; ?>
                </td>
                <td><?= $row['remark'] ?></td>
                <td>
                    <button class="btn btn-danger btn-sm deletePayment"
                            data-id="<?= $row['id'] ?>">
                        Delete
                    </button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>





