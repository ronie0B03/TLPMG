<?php
require_once 'process_employee.php';

include('sidebar.php');
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI;

$getDevices = $mysqli->query("SELECT * FROM devices WHERE deleted='0' ORDER BY price DESC") or die ($mysqli->error);
$getEmployeeApplication = $mysqli->query('SELECT e.id, e.employee_id, e.full_name, e.months, e.application_date, d.brand, d.model, d.price FROM employee e
    JOIN devices d
    ON e.device_id = d.id') or die ($mysqli->error);

?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <?php
        include('topbar.php');
        ?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Employee</h1>
            </div>

            <!-- Alert here -->
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php } ?>
            <!-- End Alert here -->

            <!-- Add Employee -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add Employee Loan Application</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="post" action="process_employee.php">
                            <table class="table" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th width="25%">ID</th>
                                    <th width="25%">Full Name</th>
                                    <th width="25%">Device / Laptop</th>
                                    <th width="25%">No. of Months</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input type="text" name="id" class="form-control" placeholder="ex: 0505050" value="<?php if($updateApplication){echo $employee_id;} ?>" required></td>
                                    <td><input type="text" name="fullname" class="form-control" placeholder="ex: Juan Cruz ZXC" value="<?php if($updateApplication){echo $full_name;} ?>" required></td>
                                    <td>
                                        <select class="form-control" name="device" required>
                                            <option value="" required disabled selected>Select</option>
                                            <?php while($newDevices=$getDevices->fetch_assoc()){ ?>
                                                <option value="<?php echo $newDevices['id']; ?>">
                                                    <?php echo '₱'.number_format($newDevices['price'],2).' - '.$newDevices['brand'].' - '.$newDevices['model']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td><input type="number" name="months" class="form-control" min="0" max="12" placeholder="ex: 12" value="<?php if($updateApplication){echo $months;} ?>" required></td>
                                </tr>
                                </tbody>
                            </table>
                            <?php if(!$updateApplication){ ?>
                                <button class="float-right btn btn-sm btn-primary m-1" name="save" type="submit"><i class="far fa-save" ></i> Save</button>
                            <?php } else { ?>
                                <input type="text" value="<?php echo $applicattion_id; ?>" style="visibility: hidden" name="application_id" readonly>
                                <button class="float-right btn btn-sm btn-primary m-1" name="update" type="submit"><i class="far fa-save" ></i> Update</button>
                            <?php } ?>
                                <a href="employee.php" class="btn btn-danger btn-sm m-1 float-right"><i class="fas as fa-sync"></i> Clear/Refresh</a>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Add Employee -->

            <!-- List of Employees -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List of Employees</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="employeeTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Laptop</th>
                                <th>Price</th>
                                <th>Balance</th>
                                <th>Mos. remaining</th>
                                <th>Due this month</th>
                                <th>Application Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while($newEmployeeApplication=$getEmployeeApplication->fetch_assoc()){ ?>
                                <tr>
                                    <td><?php echo $newEmployeeApplication['employee_id']; ?></td>
                                    <td><?php echo $newEmployeeApplication['full_name']; ?></td>
                                    <td><?php echo $newEmployeeApplication['brand'].' '.$newEmployeeApplication['model']; ?></td>
                                    <td>₱<?php echo number_format($newEmployeeApplication['price'],2); ?></td>
                                    <td><?php echo $newEmployeeApplication['price']; ?> minus Balance </td>
                                    <td><?php echo $newEmployeeApplication['months']; ?> minus months of paid</td>
                                    <td><?php echo $newEmployeeApplication['price']; ?> Balance divide by no of mos remaining</td>
                                    <td><?php echo $newEmployeeApplication['application_date']; ?></td>
                                    <td>
                                        <!-- Start Drop down Delete here -->
                                        <a href="employee.php?edit=<?php echo $newEmployeeApplication['id']; ?>" class="btn btn-info btn-sm mb-1"><i class="far fa-edit"></i> Edit</a>
                                        <button class="btn btn-danger btn-secondary dropdown-toggle btn-sm mb-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="far fa-trash-alt"></i> Delete
                                        </button>
                                        <div class="dropdown-menu p-1" aria-labelledby="dropdownMenuButton btn-sm">
                                            Are you sure you want to delete? You cannot undo the changes<br/>
                                            <a href="process_employee.php?delete=<?php echo $newEmployeeApplication['id'] ?>" class='btn btn-danger btn-sm'>
                                                <i class="far fa-trash-alt"></i> Confirm Delete
                                            </a>
                                            <a href="#" class='btn btn-success btn-sm'><i class="far fa-window-close"></i> Cancel</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Employees -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- JS here -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#employeeTable').DataTable( {
                "pageLength": 25
            } );
        } );
    </script>
    <?php
    include('footer.php');
    ?>
    <style type="text/css">
        /*
        Max width before this PARTICULAR table gets nasty. This query will take effect for any screen smaller than 760px and also iPads specifically.
        */
        @media
        only screen
        and (max-width: 760px), (min-device-width: 768px)
        and (max-device-width: 1024px)  {

            /* Force table to not be like tables anymore */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin: 0 0 1rem 0;
            }

            tr:nth-child(odd) {
                padding: 1%;
                width: 100%;
                border-bottom: 2px solid grey;
                border-top: 2px solid grey;
                background: #fcfce3;
            }

            td {
                border-bottom: 1px solid #eee;
                position: relative;
            }

            td:before {
                top: 0;
                width: 45%;
                padding-right: 5%;
                white-space: nowrap;
            }

            /*
            Label the data
            You could also use a data-* attribute and content for this. That way "bloats" the HTML, this way means you need to keep HTML and CSS in sync.
            */
            td:nth-of-type(1):before { content: "ID:"; font-weight: bold;}
            td:nth-of-type(2):before { content: "Full Name:"; font-weight: bold; }
            td:nth-of-type(3):before { content: "Building Name:"; font-weight: bold; }
            td:nth-of-type(4):before { content: "Building Description:"; font-weight: bold; }
            td:nth-of-type(4):before { content: "Building Description:"; font-weight: bold; }

        }
    </style>
