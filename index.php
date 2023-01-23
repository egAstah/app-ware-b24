<!doctype html>
<html lang="en">
<?
include 'function.php';
$arSelect = array("*", 'PROPERTY_*');
$arFilter = array("IBLOCK_ID" => 14, "ACTIVE" => "Y");
$iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
$arrRes = [];
while ($ob = $iblock->GetNextElement()) {
    $arFields = $ob->GetFields();
    $section = $arFields['IBLOCK_SECTION_ID'];
    $res = CIBlockSection::GetByID($section);
    if($ar_res = $res->GetNext())
        $arrRes[$ar_res['ID']] = $ar_res['NAME'];
}
$arrRes = array_unique($arrRes);
?>

<head>
    <title>Title</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <div class="container p-3">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <button type="button" class="btn btn-warning btn-sm me-3" id="wareAll">Основной склад</button>
                        <button type="button" class="btn btn-primary btn-sm" id="selectUser">Склад сотрудника</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success btn-sm me-3" id="updateWare">Обновить склад</button>
                        <button type="button" class="btn btn-danger btn-sm me-3" data-bs-toggle="modal"
                            data-bs-target="#modalId">Списание товаров</button>
                        <button type="button" class="btn btn-success btn-sm" id="saveRows">Сохранить</button>
                    </div>
                </div>
                <input type="hidden" id="user">
                <h5 class="mt-3 mb-3"></h5>
                <div>
                    <? foreach ($arrRes as $key => $item):?>
                        <button class="btn-folder" id="folder-items" data-id="<?=$key?>"><i class="fa-solid fa-folder-open"></i> <?=$item?></button>
                    <?endforeach;?>
                </div>
                <table class="table table-bordered table-hover table-ware mt-3">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Количество</th>
                        </tr>
                    </thead>
                    <tbody class="product-table">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitleId">Списание товаров</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-modal table-bordered">
                            <thead>
                                <tr>
                                    <th>Товар</th>
                                    <th>Количество</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= listMaterial(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="sendSoglasovanie">Отправить</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
            integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
            </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
            integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
            </script>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js"
            integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script src="//api.bitrix24.com/api/v1/"></script>
        <script src="https://kit.fontawesome.com/b675a8d36a.js" crossorigin="anonymous"></script>
        <script src="main.js"></script>
</body>

</html>