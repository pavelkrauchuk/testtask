<?php /** @var array $data */?>

<p>Нажми на кнопку и получи случайный приз</p>
<form method="post" action="/getPrize">
    <input type="submit" value="Получить приз">
</form>

<form method="post" action="/logout">
    <input type="submit" value="Выход">
</form>

<p>Ваш бонусный счет составляет: <?= $data['bonus_account'] ?? 0; ?></p>

<?php if (isset($data['prizes']) && is_array($data['prizes'])) {
    echo <<<HTML
<table border>
    <thead>
        <tr>
            <th>Тип приза</th>
            <th>Количество</th>
            <th>Название</th>
            <th>Доступные действия</th>
        </tr>
    </thead>
HTML;

    foreach ($data['prizes'] as $prize) {
        switch ($prize['type']) {
            case 'money':
                require 'templates/prize/money.php';
                break;
            case 'bonus':
                require 'templates/prize/bonus.php';
                break;
            case 'thing':
                require 'templates/prize/thing.php';
                break;
        }
    }
} else {
    echo '<p>Вы еще не получили ни одного приза</p>';
}

echo "</table>"; ?>