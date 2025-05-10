<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('tcpdf/tcpdf.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $letterContent = $_POST['letter_content'] ?? '';

    // Save raw HTML to session
    $_SESSION['custom_letter_html'] = $letterContent;

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->AddPage();
    $pdf->writeHTML($letterContent, true, false, true, false, '');

    // Ensure letters folder exists
    if (!file_exists('letters')) {
        mkdir('letters', 0777, true);
    }

    // Save PDF file
    $relativePath = 'letters/letter_' . time() . '_' . rand(1000, 9999) . '.pdf';
    $absolutePath = __DIR__ . '/../letters/' . basename($relativePath); // full path

    $pdf->Output($absolutePath, 'F');

    // Save relative path for database use
    $_SESSION['custom_letter_path'] = $relativePath;

    // Redirect to checkout
    header("Location: checkout.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/custom-letter.css" />
    <title>Custom Letter</title>
    <style>
        *{
        color: <?php echo $fontColor; ?>;
        }
        body {
        background-color: <?php echo $bgColor; ?>;
        }
    </style>
  <script src="https://cdn.ckeditor.com/4.21.0/standard-all/ckeditor.js"></script>
</head>
<body>
  <h1>Create Your Digital Letter</h1>

  <form method="post" action="custom-letter.php">
    <textarea name="letter_content" id="letter_content"></textarea><br><br>
    <button type="submit">Save Letter & Go Back</button>
  </form>

  <hr>

  <h2>Live Preview</h2>
  <div id="preview" style="border:1px solid #ccc; padding:10px; min-height:100px;"></div>

  <script>
    CKEDITOR.replace('letter_content', {
      extraPlugins: 'emoji',
      removePlugins: 'image,about'
    });

    setInterval(() => {
      const html = CKEDITOR.instances.letter_content.getData();
      document.getElementById('preview').innerHTML = html;
    }, 1000);
  </script>
</body>
</html>