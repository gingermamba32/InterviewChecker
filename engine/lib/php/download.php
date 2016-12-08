<?
    header('Content-type: application/octet-stream');
    include('_base.php');
    
    $id = intval($_GET['id']);
    $fn = $_GET['name'];


    $Q = $D->One("SELECT * FROM `WL_Files` WHERE `id` = $id");
    if (!$Q) Error(404, 'File not found');
        
    readfile(INDEX_DIR.'/data/files/'.$Q['file']);
    
?>