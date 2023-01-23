<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/wareAllUser/dompdf/autoload.inc.php');

function callMethod($queryMethod, $data)
{
    $queryUrl = 'https://mosgeizer24.ru/rest/1/h44729z3wjz2fr20/' . $queryMethod;
    $queryData = http_build_query($data);

    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        )
    );

    $result = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($result, 1);

    return $result;
}
$date = new DateTime();
$date->add(new DateInterval('P1D'));
$date = $date->format('Y-m-d');

$crmList = callMethod('crm.item.list', ['entityTypeId' => 164, 'filter' => [
    '>=ufCrm3_1662734979873' => $date . ' 00:00:00',
    '<=ufCrm3_1662734979873' => $date . ' 23:59:00'
]])['result']['items'];

$productArr = [];

foreach ($crmList as $item) {
    $getProduct = callMethodInit('crm.item.productrow.list', [
        'filter' => [
            '=ownerType' => 'Ta4',
            '=ownerId' => $item['id']
        ]
    ])['result']['productRows'];
    $arUser = CUser::GetByID($item['assignedById'])->fetch();
    $productArr[] = [
        'user' => $arUser['NAME'] . ' ' . $arUser['LAST_NAME'],
        'arr' => $getProduct
    ];
}

$table = '';
foreach ($productArr as $item) {
    $productTable = '';
    $table .= '
        <h3>Сотрудник: '.$item['user'].'</h3>
        <table class="list">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Количество</th>
                </tr>
            </thead>
            <tbody>
    ';
    foreach ($item['arr'] as $arr) {
        $productTable .= '
            
            <tr class="grid-block">
                <td>'.$arr['productName'].'</td>
                <td>'.$arr['quantity'].'</td>
            </tr>
        ';
    }
    $table .= $productTable . '
        </tbody>
        </table>
    ';
//    $table .= $productTable;
}

//
$html = '
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	
	<style type="text/css">
	* {font-family: dejavu serif;font-size: 14px;line-height: 14px;}
	table {margin: 0 0 15px 0;width: 100%;border-collapse: collapse;border-spacing: 0;}		
	table th {padding: 5px;font-weight: bold;}        
	table td {padding: 5px;}
	.list thead, .list tbody  {border: 1px solid #000;}
	.list thead th {padding: 5px;border: 1px solid #000;vertical-align: middle;text-align: left; width: 50%;}	
	.list tbody td {padding: 5px;border: 1px solid #000;vertical-align: middle;font-size: 14px;line-height: 13px; width: 50%;}	
	</style>
</head>
<body>
	'.$table.'
</body>
</html>';
//echo $html;
$dompdf = new Dompdf\Dompdf();
$dompdf->set_option('isRemoteEnabled', TRUE);
$dompdf->setPaper('A4', 'portrait');
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->render();
//print_r($dompdf);
$pdf = $dompdf->output();
//$dompdf->stream("hello.pdf");
file_put_contents(__DIR__ . '/112.pdf', $pdf);

$date = date('d.m.Y', strtotime($date));

$idTask = callMethod('tasks.task.add', ['fields' => [
    'TITLE' => 'Пополнение складов сотрудников на ' . $date,
    'RESPONSIBLE_ID' => 1,
    'CREATED_BY' => 1
]])['result']['task']['id'];

callMethod('task.item.addfile', [
    'TASK_ID' => $idTask,
    'FILE[NAME]' => 'Задание на сборку.pdf',
    'FILE[CONTENT]' => base64_encode(file_get_contents(__DIR__ .'/112.pdf'))
]);
//file_put_contents(__DIR__ . '/112.pdf', $pdf);