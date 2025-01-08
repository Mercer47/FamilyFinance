<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view orders</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- custom css file link -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css"
        integrity="sha512-/zs32ZEJh+/EO2N1b0PEdoA10JkdC3zJ8L5FTiQu82LR9S/rOQNfQN7U59U9BC12swNeRAz3HSzIL2vpp4fv3w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"
        integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>

<body>

    <?php $this->view('includes/header'); ?>







    <div class="order_main">

        <section class="shopping-cart">
            <h1 class="heading" style="color:  #2A9D8F;">Total Expense</h1>
            <?php if (isset($message)): ?>
                <p><?= $message; ?></p>
            <?php endif; ?>
            <div class="he-form">


                <div class="forms3">
                    <form method="post" action="<?php echo base_url('totalexpense/fetchtype'); ?>" class="mb-4">

                        <select id="type" name="type" class="form-control" required onchange="toggleGoButton()">
                            <option value="" id="vlu">--Select Expense Type--</option>
                            <?php foreach ($expense_types as $expense): ?>
                                <option value="<?= $expense['type']; ?>">
                                    <?= $expense['type']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button name="go" class="btn btn-info btns" style="display: none;" id="btn22">GO</button>
                    </form>

                </div>
                <h1 class="hie">
                    Total Expenses: <?= isset($total_expense) ? '$' . number_format($total_expense, 2) : '$0.00'; ?>
                </h1>

                <!-- Display total expense for the selected type if available -->
                <?php if (isset($selected_type) && isset($selected_type_expense)): ?>
                    <h1 class="heading" style="color: #E76F51; font-size:15px; margin-left:50px;">
                        Total Expense for <?= htmlspecialchars($selected_type); ?>:
                        $<?= number_format($selected_type_expense, 2); ?>
                    </h1>
                <?php endif; ?>
            </div>
            <div class="user">
                <table>

                    <thead>
                        <th>id</th>
                        <th>Expense Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Remarks</th>
                        <th>Action</th>



                    </thead>

                    <tbody>
                        <?php if (!empty($view)): ?>
                            <?php foreach ($view as $view): ?>
                                <tr>
                                    <td><?= $view['user_id']; ?></td>
                                    <input type="hidden" name="type" value="<?= $view['type']; ?>">
                                    <td><?= $view['type']; ?></td>
                                    <td style="color:  red;">$<?= $view['amount']; ?></td>
                                    <td><?= $view['created_at']; ?></td>
                                    <td><?= $view['remarks']; ?></td>
                                    <td> <a href="<?php echo base_url('expense/edit/') . $view['id']; ?>"
                                            class="btn btn-info btns">Edit</a>
                                        &nbsp;
                                        <a href="<?php echo base_url('expense/delete/') . $view['id']; ?>"
                                            class="btn btn-danger btns"
                                            onclick="return confirm('Are you sure you want to delete this?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No records found please selected expense type.</td>
                            </tr>
                        <?php endif; ?>


                    </tbody>
                </table>
            </div>


        </section>
    </div>




    <canvas id="myChart"></canvas>

    <script>
        // Extract data for the chart from the PHP variables
        const weeklyLabels = <?php echo json_encode($weekly_expenses['labels']); ?>;
        const weeklyAmounts = <?php echo json_encode($weekly_expenses['amounts']); ?>;



        // Prepare data for the chart
        const chartData = {
            labels: weeklyLabels, // 7-day periods on the X-axis
            datasets: [{
                label: 'Weekly Expenses',
                data: weeklyAmounts, // Sum of amounts per 7-day period
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Bar color
                borderColor: 'rgba(75, 192, 192, 1)', // Border color
                borderWidth: 1
            }]
        };

        const ctx = document.getElementById('myChart').getContext('2d');
        const expenseChart = new Chart(ctx, {
            type: 'bar', // Bar chart type
            data: chartData,
            options: {
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Date Range'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount'
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Hide legend if not needed
                    }
                }
            }
        });
    </script>




    <!-- pie chart -->
    <canvas id="pieChart"></canvas>
    <script>
        // Prepare labels (expense types) and data (created_at dates) for the chart
        let expenseAmountsPie = [];
        let expenseTypes = [];


        // Extract expense types and dates from the PHP data passed to JavaScript
        <?php foreach ($types as $expense): ?>
            expenseAmountsPie.push("<?php echo $expense['amount']; ?>");
            expenseTypes.push("<?php echo $expense['type']; ?>");
        <?php endforeach; ?>



        // Prepare data for the chart
        let pieChartData = {
            labels: expenseTypes, // Dates on the X-axis
            datasets: [{
                label: 'Expenses by Types',
                data: expenseAmountsPie, // Amount as the data points
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Create the chart
        let pieCtx = document.getElementById('pieChart').getContext('2d');
        let pieChart = new Chart(pieCtx, {
            type: 'pie', // Pie chart type
            data: pieChartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true // Show legend for pie chart
                    }
                }
            }
        });
    </script>


    <!-- pie chart -->




    <!-- line chart -->


    <!-- Line chart for monthly data -->
    <canvas id="lineChart"></canvas>
    <script>
        // Prepare labels (dates) and data (amounts) for the chart
        const monthlyLabels = <?php echo json_encode($monthly_expenses['labels']); ?>;
        const monthlyAmounts = <?php echo json_encode($monthly_expenses['amounts']); ?>;

        // Prepare data for the line chart
        const lineChartData = {
            labels: monthlyLabels.map(date => date.split('-').slice(2).join('/')),  // Show "01", "02", etc.
            datasets: [{
                label: 'Monthly Expenses',
                data: monthlyAmounts, // Individual amounts per day across multiple months
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Line fill color
                borderColor: 'rgba(75, 192, 192, 1)', // Line border color
                borderWidth: 2,
                fill: true,
                tension: 0.4 // Adjust tension for a wave-like form
            }]
        };
        // Create the line chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line', // Line chart type
            data: lineChartData,
            options: {
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Date'
                        },
                        ticks: {
                            autoSkip: false, // Do not skip dates to ensure continuity across months
                            maxTicksLimit: 30, // Show up to 30 dates per month (adjust as needed)
                            callback: function (value, index, values) {
                                // Display only the first day of each month for clarity
                                const date = new Date(value);
                                return date.getDate() === 1 ? value : '';
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount'
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: true // Show legend for line chart
                    }
                }
            }
        });
    </script>



    <!-- line chart -->



    <!-- table for pie chart -->




    <!-- table for pie chart -->
    <!-- Table to display expense types and amounts -->





    <!-- monthly table -->


    <div class="order_main">
    <section class="shopping-cart">
        <h1 class="heading" style="color: #2A9D8F;">Monthly Expense Type</h1>

        <!-- Month Selection Dropdown -->
        <form method="get" action="" class="mb-4">
            <select name="month" id="month" onchange="this.form.submit()">
                <option value="">-- Select a Month --</option>
                <?php
                // Loop to generate the month options
                for ($i = 0; $i < 12; $i++) {
                    // Format month as 'Y-m' (e.g., '2025-01' for January 2025)
                    $month_value = date('Y-m', strtotime("-$i months"));
                    // Extract the month name
                    $month_name = date('F', strtotime($month_value));
                    // Check if the current option is selected
                    $selected = ($month_value === $selected_month) ? 'selected' : '';
                    echo "<option value='$month_value' $selected>$month_name</option>";
                }
              
                ?>
            </select>
        </form>

      <!-- Show selected month name -->
<?php if ($selected_month): ?>
    <p style="font-size:18px;">
        <strong>Selected Month: </strong>
        <?php 
            // Ensure selected_month is correctly formatted
            $month_name = date('F', mktime(0, 0, 0, intval($selected_month), 1)); 
            echo $month_name; 
        ?>
    </p>
<?php endif; ?>
        


        <!-- Show table only if a month is selected -->
        <?php if ($selected_month && !empty($month_types)): ?>
            <div class="user">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Expense Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalAmount = 0;
                        foreach ($month_types as $expense):
                            $totalAmount += $expense['amount'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($expense['type']); ?></td>
                                <td><?php echo number_format($expense['amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="font-size:20px;">TOTAL</th>
                            <th style="font-size:20px;">
                                <?php echo number_format($totalAmount, 2); ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php elseif ($selected_month): ?>
            <p>No expenses found for the selected month.</p>
        <?php endif; ?>
    </section>
</div>



    <!-- monthly table -->

    <div class="order_main">

        <section class="shopping-cart">
            <h1 class="heading" style="color:  #2A9D8F;">Expense Type</h1>


            <div class="user">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Expense Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalAmount = 0; // Initialize total amount
                        foreach ($types as $expense):
                            $totalAmount += $expense['amount']; // Accumulate the total
                            ?>

                            <tr>
                                <td><?php echo htmlspecialchars($expense['type']); ?></td>
                                <td><?php echo number_format($expense['amount'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="font-size:20px; margin: 20px auto; ">TOTAL</th>
                            <th style="font-size:20px; margin: 20px auto; ">
                                <?php echo number_format($totalAmount, 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>


        </section>
    </div>

    



    <script>

        // Toggle the display of the GO button based on dropdown selection
        function toggleGoButton() {
            const selectElement = document.getElementById('type');
            const goButton = document.getElementById('btn22');
            goButton.style.display = selectElement.value ? 'inline' : 'none';
        }



        // Check if the session has the username and display it
        <?php if ($this->session->userdata('name')): ?>
            const loginBtn = document.getElementById('login_btn');
            log_out = document.getElementById('log_out');
            log_out.style.display = 'block';
            const username = "<?php echo $this->session->userdata('name'); ?>";
            loginBtn.innerHTML = username; // Set the username
            loginBtn.style.display = 'inline'; // Show the button
        <?php else: ?>
            const loginBtn = document.getElementById('login_btn');
            loginBtn.innerHTML = "Login"; // Show default login text
            loginBtn.style.display = 'inline'; // Ensure it's visible
        <?php endif; ?>
    </script>

</body>

</html>