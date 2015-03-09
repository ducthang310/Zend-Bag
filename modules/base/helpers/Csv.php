<?php

class Base_Helper_Csv
{
    public static function downloadCsv($headings, $array)
    {
        $fh = fopen('php://output', 'w');
        ob_start();
        $ReportHeading = array(
            "REPORT PICK UP"
        );
        $blank = array();
        foreach($array as $key => $value){
            fputcsv($fh, array($key));
            foreach($value as $title => $items){
                fputcsv($fh, array($title));
                fputcsv($fh, $headings);
                if($items != null){
                    $i = 1;
                    foreach($items as $item){
                        $data['customer_name'] = str_replace(",","",$item['customer_search_name']);
                        $data['courier_name'] = str_replace(",","",$item['courier_search_name']);
                        $data = array(
                            $i,$item['id'], $data['customer_name'] , $data['courier_name'],
                            $item['from_address'], $item['to_address'], Pickup_Constant_Server::$_STATUS[$item['status']],
                            '$ '.$item['base_charge_fee'],  '$ '.$item['delivery_signature_fee'],   '$ '.$item['delivery_insure_fee'],
                            '$ '.$item['credit_fee'], '$ '.$item['total_fee'],
                        );
                        fputcsv($fh, $data);
                        $i++;
                    }
                    fputcsv($fh, $blank);
                }
                fputcsv($fh, $blank);
            }
        }

        $string = ob_get_clean();
        ob_end_clean();
        $filename = 'csv_' . date('Ymd') . '_' . date('His');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename.csv\";");
        header("Content-Transfer-Encoding: binary");
        echo $string;
        fclose($fh);
        exit(0);
    }
}