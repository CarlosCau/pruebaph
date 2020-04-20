<?php
    include "../../includes/init.php";
    if (isset($_POST['tbl'])) {
        $table = $_POST['tbl'];
        if (isset($_POST['user'])){
            $user = $_POST['user'];
            try {
                $result = $pdo->query("SELECT id, nombre FROM {$table} ORDER BY nombre ASC");
                $returnTable="<table class='table table-hover'>";
                $returnTable.="<tr class='tblHeader'><th>COLONIA</th><th></th><th></th><th></th></tr>";
                foreach($result AS $row) {
                    $returnTable.="<tr><td>{$row['nombre']}</td><td><span class='btnFindGen'  data-id='{$row{'id'}}' data-table='{$table}'><i class='fa fa-search'></i></span></td><td><span class='btnNavGen'  data-id='{$row{'id'}}' data-table='{$table}'><i class='fa fa-ship'></i></span></td><td><span class='btnEditGen' data-id='{$row{'id'}}' data-table='{$table}'><i class='fa fa-pencil'></i></span></td><td><span class='btnDeleteGen' data-id='{$row{'id'}}' data-table='{$table}'><i class='fa fa-trash'></i></span></td></tr>";
                }
                $returnTable.="</table>";
                echo $returnTable;
            } catch(PDOException $e) {
                echo "ERROR: ".$e->getMessage();
            }
        } else {
            echo "ERROR: No user parameter incuded with request";
        }
    } else {
        echo "ERROR: No table parameter incuded with request";
    }
?>