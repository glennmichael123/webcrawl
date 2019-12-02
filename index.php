<?php
include 'vendors/simple_html_dom.php';
include 'vendors/Curl.php';
include 'src/Client.php';

// Initialize Client class and access login
$client      = new Client();
$scrapedData = $client->login();

// Get the other table from JSON.
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $scrapedData['tableData'] = $client->getDataJSON();
}
?>

<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="UTF-8">
    <title>Bbo - Web Crawler</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css"/>
</head>

<body>
    <div class="container">
        <div style="margin-top: 50px;" class="row">
            <!-- If update is clicked only display back button, otherwise only display update button -->
            <?php if($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <a class="btn btn-default" href="index.php">Back</a>
            <?php else: ?>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            <?php endif;?>
        </div>

        <!-- Table 1 and Table 2 -->
        <?php if($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <h1 style="text-align: center">This is table 2</h1>
        <?php else: ?>
            <h1 style="text-align: center">This is table 1</h1>
        <?php endif; ?>

        <div class="row" style="margin-top: 25px;">
            <div class="col-md-12">
                <table class="table table-responsive table-striped">
                    <tr>   
                        <!-- Iterate throught table headers -->
                        <?php foreach($scrapedData['headers'] as $th): ?>
                            <th><?= $th ?></th> 
                        <?php endforeach; ?>
                    </tr>

                     <!-- Iterate through different dates -->
                    <?php foreach ($scrapedData['tableData'] as $k => $rows): ?>
                        <!-- Check if key exists cause there is an array with empty data -->
                        <?php if($k): ?>
                            <tr>
                                <td data-value="<?= explode('/', $k)[0]; ?>" style="font-weight: bold; font-size: 25px; text-align: center;" colspan="<?= count($scrapedData['headers'])?>">
                                    <?= $k ?> <a class="to-hide" href="#" style="font-size: 11px;">Toggle</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <!-- Iterate through rows -->
                        <?php foreach ($rows as $key => $row): ?>
                            <tr class="<?= explode('/', $k)[0]; ?>"> 
                                <!-- Iterate through table data -->
                                <?php foreach ($row as $td): ?>
                                    <td> <?= $td ?> </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        $(document).on('click', '.to-hide', function(e) {
            let cl  = $(this).closest('td');
            let val = cl.data('value');

            $(`.${val}`).toggle();
        })
    </script>
</body>
</html>
