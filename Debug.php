<?php
/**
 * NOTES
 * outputs variables passed
 */
function outputVars($variables){

    $outputString = "";
    foreach ($variables as $v) {
        $outputString .= $v . " ";
    } ?>
    <script>alert("debugging: <?=$outputString?>");</script>
    <?php
}
    
?>