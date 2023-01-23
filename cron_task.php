<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

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

echo '<pre>';

$arSelect = array("*", 'PROPERTY_*');
$arFilter = array("IBLOCK_ID" => 16, "ACTIVE" => "Y", '!ID' => 31);
$iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

$resultArr = [];

while ($ob = $iblock->GetNextElement()) {
    $arFields = $ob->GetFields();
    $arProps = $ob->GetProperties();
    $json = html_entity_decode($arProps['63']['VALUE']);
    $resultArr[] = [
        'user' => $arProps['62']['VALUE'],
        'arr' => json_decode($json, true)
    ];
}

$arProduct = [];

foreach($resultArr as $item){
    foreach($item['arr'] as $prod){
        if($prod['TYPE'] == 'Y'){
            $arProduct[] = [
                'user' => $item['user'],
                'prod' => $prod['NAME'] . '(' . $prod['QTY'] . ')'
            ];
        }
    }
}

$result = [];
foreach ($arProduct as $row)
{
    $result[$row['user']][] = $row['prod'];
}


foreach($result as $key => $item){
    
    $date = new DateTime();
    $date->add(new DateInterval('P1D'));

    $idTask = callMethod('tasks.task.add', ['fields' => [
        'TITLE' => 'Вернуть товары на склад',
        'RESPONSIBLE_ID' => $key,
        'CREATED_BY' => 1,
        'DEADLINE' => $date->format('Y-m-d 09:00:00'),
        'UF_AUTO_570508322711' => 1
    ]])['result']['task']['id'];

    foreach($item as $prod){
        callMethod('task.checklistitem.add', [$idTask, ['TITLE' => $prod]]);
    }
}
