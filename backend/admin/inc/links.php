    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/common.css">
    <?php
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $settings_q = "SELECT * FROM `settings` WHERE `sr_no` = ?";
        $values = [1];
        $settings_r = mysqli_fetch_assoc(select($settings_q, $values, "i"));
        if ($settings_r['shutdown'])
        {
            echo <<<alertbar
                <div class="bg-danger text-center p-2 fw-bold">
                <i class="bi bi-exclamation-triangle-fill"></i>
                   
                </div>
            alertbar;
        }
    ?>