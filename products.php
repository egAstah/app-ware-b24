<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");


$arSelect = array("*", 'PROPERTY_*');
$arFilter = array("IBLOCK_ID" => 17, "ACTIVE" => "Y", 'ID' => $_GET['id']);
$iblock = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
while ($ob = $iblock->GetNextElement()) {
    $arFields = $ob->GetFields();
    $arProps = $ob->GetProperties();
    $json = html_entity_decode($arProps['64']['VALUE']);
}
$arMaterial = json_decode($json, true);
$jsonInput = json_encode($arMaterial);
?>


<!doctype html>
<html lang="en">

<head>
    <title>Заявка на согласование <?= $_GET['id'] ?>
    </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

</head>

<body>
    <div class="container pt-3 pb-3">
        <div class="row">
            <h5>Заявка на согласование № <?= $_GET['id'] ?>
            </h5>
            <input type="hidden" id="id" value="<?= $_GET['id'] ?>">
            <input type="hidden" id="type" value="<?= $_GET['type'] ?>">
            <input type="hidden" id="user" value="<?= $_GET['user'] ?>">
            <div class="col-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th scope="col">Наименование</th>
                        <th scope="col">Количество</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($arMaterial as $item): ?>
                    <tr>
                        <td>
                            <?= $item['NAME'] ?>
                        </td>
                        <td>
                            <?= $item['QTY'] ?>
                        </td>
                    </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn-success" id="save">Согласовать</button>
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
    <script>
        $(document).on('click', '#save', function () {
            $.ajax({
                url: './function.php',
                method: 'post',
                dataType: 'html',
                data: { 'event': 'spisanie-soglas', id: $('#id').val(), type: $('#type').val(), user: $('#user').val()},
                success: function (data) {
                    // console.log(data)
                    window.close()
                }
            });
        })
    </script>
</body>

</html>