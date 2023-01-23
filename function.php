<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
CModule::IncludeModule("crm");

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

function listMaterial()
{
    $html = '';
    $arSelect = array("*", 'PROPERTY_*');
    $arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y");
    $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    while ($ob = $iblock->GetNextElement()) {
        $arFields = $ob->GetFields();
        $html .= '
            <tr class="list-products-modal" id="' . $arFields['ID'] . '">
                <td class="form-label" data-id="' . $arFields['ID'] . '">' . $arFields['NAME'] . '</td>
                <td class="count-product" data-id="' . $arFields['ID'] . '">
                    <input type="number" min="0" class="form-control"  data-id="' . $arFields['ID'] . '" value="0">
                </td>
            </tr>
        ';
    }
    return $html;
}

global $USER;
switch ($_POST['event']) {
    case 'user-ware':
        $html = '';
        if ($_POST['dataWare'] == 'user') {
            $arSelect = array("*", 'PROPERTY_*');
            $arFilter = array("IBLOCK_ID" => 16, "ACTIVE" => "Y", 'PROPERTY_62' => $_POST['id']);
            $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            if ($iblock->SelectedRowsCount() < 1) {
                $arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y", 'IBLOCK_SECTION_ID');
                $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                while ($ob = $iblock->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    $type = $arProps['REGULYARNYY_TOVAR_1P9LD2']['VALUE'];
                    if ($type == '' || $type == 'N')
                        $type = 'N';
                    else
                        $type = 'Y';
                    $html .= '
                    <tr class="list-products"  id="' . $arFields['ID'] . '" data-name="' . $arFields['NAME'] . '">
                        <td class="form-label" data-id="' . $arFields['ID'] . '" data-type="' . $type . '">' . $arFields['NAME'] . '</td>
                        <td class="count-product" data-id="' . $arFields['ID'] . '"><input type="number" min="0" class="form-control"  data-id="' . $arFields['ID'] . '" value="0"></td>
                    </tr>
                ';
                }
                echo $html;
            } else {
                while ($ob = $iblock->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    $json = html_entity_decode($arProps['63']['VALUE']);
                }
                $arMaterial = json_decode($json, true);
                $arrAllProduct = [];
                $arIdProduct = [];
                foreach ($arMaterial as $key => $item) {
                    $arIdProduct[] = $item['ID'];
                }
                $arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y", '!ID' => $arIdProduct);
                $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                while ($ob = $iblock->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    $type = $arProps['REGULYARNYY_TOVAR_1P9LD2']['VALUE'];
                    if ($type == '' || $type == 'N')
                        $type = 'N';
                    else
                        $type = 'Y';
                    $arrAllProduct[] = [
                        'ID' => $arFields['ID'],
                        'NAME' => $arFields['NAME'],
                        'QTY' => 0,
                        'TYPE' => $type
                    ];
                }
                $result = array_merge($arMaterial, $arrAllProduct);
                foreach ($result as $item) {
                    $html .= '
                        <tr class="list-products"  id="' . $item['ID'] . '" data-name="' . $item['NAME'] . '">
                            <td class="form-label" data-id="' . $item['ID'] . '" data-type="' . $item['TYPE'] . '">' . $item['NAME'] . '</td>
                            <td class="count-product" data-id="' . $item['ID'] . '"><input type="number" min="0" class="form-control"  data-id="' . $item['ID'] . '" value="' . $item['QTY'] . '"></td>
                        </tr>
                    ';
                }
                echo $html;
            }
        } else {
            $arFilter = array("IBLOCK_ID" => 16, "ACTIVE" => "Y", 'ID' => 31);
            $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            while ($ob = $iblock->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                $json = html_entity_decode($arProps['63']['VALUE']);
            }
            $arMaterial = json_decode($json, true);
            if (count($arMaterial) == 0) {
                $arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y", 'ID' => $arSection);
                $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                while ($ob = $iblock->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    $type = $arProps['REGULYARNYY_TOVAR_1P9LD2']['VALUE'];
                    if ($type == '' || $type == 'N')
                        $type = 'N';
                    else
                        $type = 'Y';
                    $html .= '
                    <tr class="list-products"  id="' . $arFields['ID'] . '" data-name="' . $arFields['NAME'] . '">
                        <td class="form-label" data-id="' . $arFields['ID'] . '" data-type="' . $type . '">' . $arFields['NAME'] . '</td>
                        <td class="count-product" data-id="' . $arFields['ID'] . '"><input type="number" min="0" class="form-control"  data-id="' . $arFields['ID'] . '" value="0"></td>
                    </tr>
                ';
                }
                echo $html;
            } else {
                while ($ob = $iblock->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    $json = html_entity_decode($arProps['63']['VALUE']);
                }
                $arMaterial = json_decode($json, true);
                $arrAllProduct = [];
                $arIdProduct = [];
                foreach ($arMaterial as $key => $item) {
                    $arIdProduct[] = $item['ID'];
                }
                $arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y", '!ID' => $arIdProduct);
                $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
                while ($ob = $iblock->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    $type = $arProps['REGULYARNYY_TOVAR_1P9LD2']['VALUE'];
                    if ($type == '' || $type == 'N')
                        $type = 'N';
                    else
                        $type = 'Y';
                    $arrAllProduct[] = [
                        'ID' => $arFields['ID'],
                        'NAME' => $arFields['NAME'],
                        'QTY' => 0
                    ];
                }
                $result = array_merge($arMaterial, $arrAllProduct);
                foreach ($result as $item) {
                    $html .= '
                        <tr class="list-products"  id="' . $item['ID'] . '" data-name="' . $item['NAME'] . '">
                            <td class="form-label" data-id="' . $item['ID'] . '" data-type="' . $item['TYPE'] . '">' . $item['NAME'] . '</td>
                            <td class="count-product" data-id="' . $item['ID'] . '"><input type="number" min="0" class="form-control"  data-id="' . $item['ID'] . '" value="' . $item['QTY'] . '"></td>
                        </tr>
                    ';
                }
                echo $html;
            }

        }
        break;
    case 'save-user-ware':
        $arProduct = [];
        print_r($_POST);
        foreach ($_POST['arr'] as $item) {
            $arProduct[] = [
                'ID' => $item['id'],
                'NAME' => $item['name'],
                'QTY' => $item['count'],
                'TYPE' => $item['type']
            ];
        }

        $json = json_encode($arProduct);

        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 16, "ACTIVE" => "Y", 'PROPERTY_62' => $_POST['user']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        while ($ob = $iblock->GetNextElement()) {
            $arFields = $ob->GetFields();
            $idElem = $arFields['ID'];
        }

        $el = new CIBlockElement;
        $PROP = [];
        $PROP[62] = $_POST['user'];
        $PROP[63] = $json;

        if ($_POST['dataWare'] == 'user') {
            if ($iblock->SelectedRowsCount() < 1) {
                $arFields = array(
                    "IBLOCK_ID" => 16,
                    "NAME" => 'Склад ' . $_POST['user'],
                    "PROPERTY_VALUES" => $PROP
                );
                $el->Add($arFields);
            } else {
                $el = new CIBlockElement;
                $arUpdate = array(
                    "MODIFIED_BY" => 1,
                    "PROPERTY_VALUES" => $PROP
                );
                $el->Update($idElem, $arUpdate);
            }
        }
        if ($_POST['dataWare'] == 'ware') {
            $el = new CIBlockElement;
            $arUpdate = array(
                "MODIFIED_BY" => 1,
                "PROPERTY_VALUES" => $PROP
            );
            $el->Update(31, $arUpdate);
        }
        break;
    case 'soglas-ware':
        $arProduct = [];
        foreach ($_POST['arr'] as $item) {
            if ($item['count'] > 0) {
                $arProduct[] = [
                    'ID' => $item['id'],
                    'NAME' => $item['name'],
                    'QTY' => $item['count']
                ];
            }
        }
        if (count($arProduct) > 0) {
            $json = json_encode($arProduct);
            $PROP = [];
            $PROP[64] = $json;
            $PROP[65] = $_POST['user'];

            $rsElement = new CIBlockElement;
            $arFields = array(
                "ACTIVE" => "Y",
                "NAME" => "Заявка на согласование от пользователя " . $_POST['user'],
                "PROPERTY_VALUES" => $PROP,
                "IBLOCK_ID" => 17
            );
            $id = $rsElement->Add($arFields);

            $result = callMethod('im.notify.system.add', [
                'USER_ID' => 1,
                'MESSAGE' => '<a href="https://mosgeizer24.ru/local/wareAllUser/products.php?id=' . $id . '&type='.$_POST['type'].'&user='.$_POST['user'].'" target="_black">Новая заявка на согласование</a>'
            ]);
        }
        break;
    case 'spisanie-soglas':
        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 17, "ACTIVE" => "Y", 'ID' => $_POST['id']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        while ($ob = $iblock->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
            $jsonSogl = html_entity_decode($arProps['64']['VALUE']);
            $idUser = $arProps['65']['VALUE'];
        }
        $arrSoglas = json_decode($jsonSogl, true);

        if($_POST['type'] == 'ware'){
            $arSelect = array("*", 'PROPERTY_*');
            $arFilter = array("IBLOCK_ID" => 16, "ACTIVE" => "Y", 'ID' => 31);
            $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            while ($ob = $iblock->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                $jsonUser = html_entity_decode($arProps['63']['VALUE']);
                $idElem = $arFields['ID'];
            }
            $arrUser = json_decode($jsonUser, true);
        }else{
            $arSelect = array("*", 'PROPERTY_*');
            $arFilter = array("IBLOCK_ID" => 16, "ACTIVE" => "Y", 'PROPERTY_62' => $_POST['user']);
            $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            while ($ob = $iblock->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                $jsonUser = html_entity_decode($arProps['63']['VALUE']);
                $idElem = $arFields['ID'];
            }
            $arrUser = json_decode($jsonUser, true);
        }
        $arUser = CUser::GetByID($_POST['user'])->fetch();
       
        $error = false; 

        foreach ($arrUser as $key => $user) {
            foreach ($arrSoglas as $item) {
                if ($user['ID'] == $item['ID']) {
                    $arFilterWare = array("IBLOCK_ID" => 14, "ACTIVE" => "Y", "ID" => $user['ID']);
                    $iblockWare = CIBlockElement::GetList(array(), $arFilterWare, false, false, $arSelect);
                    while ($obWare = $iblockWare->GetNextElement()) {
                        $arFieldsWare = $obWare->GetFields();
                        $arPropsWare = $obWare->GetProperties();
                        $minUserProduct = $arPropsWare['MINIMALNOE_KOLVO_DLYA_SKLADA_SOTRUDNIKA_H68SW1']['VALUE'];
                        $minWareProduct = $arPropsWare['MINIMALNOE_KOLVO_DLYA_OBSHCHEGO_SKLADA_6WSGME']['VALUE'];
                    }
                    $resultPrice = (int)$arrUser[$key]['QTY'] - (int)$item['QTY'];

                    if($minUserProduct != '' || $minWareProduct != ''){
                        if($_POST['type'] == 'ware'){
                            if ($resultPrice <= $minWareProduct) {
                                $result = callMethod('im.notify.system.add', [
                                    'USER_ID' => 1,
                                    'MESSAGE' => 'Достигнуто минимальное количество товара ' . $item['NAME'] . ' на общем складе'
                                ]);
                                $error = true;
                            } else {
                                $error = false;
                                if($resultPrice <= 0) $resultPrice = 0;
                                $arrUser[$key]['QTY'] = $resultPrice;
                            }
                        }else{
                            if ($resultPrice <= (int)$minUserProduct) {
                                $result = callMethod('im.notify.system.add', [
                                    'USER_ID' => 1,
                                    'MESSAGE' => 'Достигнуто минимальное количество товара ' . $item['NAME'] . ' на складе сотрудника ' . $arUser['NAME'] . ' ' . $arUser['LAST_NAME']
                                ]);
                                $error = true;
                            } else {
                                $error = false;
                                if($resultPrice <= 0) $resultPrice = 0;
                                $arrUser[$key]['QTY'] = $resultPrice;
                            }
                        }
                    }else{
                        if($resultPrice <= 0) $resultPrice = 0;
                        $arrUser[$key]['QTY'] = $resultPrice;
                    }
                }
            }
        }

        $itogJson = json_encode($arrUser);

        $el = new CIBlockElement;
        $PROP = [];
        if($_POST['type'] == 'ware')  $PROP[62] = ''; else  $PROP[62] = $_POST['user'];

        $PROP[63] = $itogJson;
        $arUpdate = array(
            "MODIFIED_BY" => 1,
            "PROPERTY_VALUES" => $PROP
        );
        if(!$error){
             $el->Update($idElem, $arUpdate);
        }
        break;
    case 'section':
        $arSelect = array("*", 'PROPERTY_*');
        $arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y", 'IBLOCK_SECTION_ID' => $_POST['id']);
        $iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arrId = [];

        while ($ob = $iblock->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arrId[] = $arFields['ID'];
        }
        echo json_encode($arrId);
        break;
}