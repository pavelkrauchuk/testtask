<?php /** @var array $prize */ ?>

<tr>
    <td><?=$prize['type'];?></td>
    <td><?=$prize['money_amount'];?></td>
    <td></td>
    <td>
    <?php if (!$prize['converted'] && !$prize['transferred']): ?>
    <form id="money_convert_<?=$prize['id'];?>" action="/convertMoney">
        <input hidden type="number" name="id" value="<?=$prize['id'];?>">
        <input type="submit" value="Конвертировать в бонусы">
    </form>
    <form id="money_transfer_<?=$prize['id'];?>" action="/transferMoney">
        <input hidden type="number" name="id" value="<?=$prize['id'];?>">
        <input type="submit" value="Перевести на счет в банке">
    </form>
    <form id="money_reject_<?=$prize['id'];?>" method="get" action="/rejectMoney">
        <input hidden type="number" name="id" value="<?=$prize['id'];?>">
        <input type="submit" value="Отказаться от приза">
    </form>
    <?php elseif ($prize['converted']): ?>
    Конвертирован в бонусы
    <?php elseif ($prize['transferred']): ?>
    Переведен на счет в банке
    <?php endif; ?>
    </td>
</tr>