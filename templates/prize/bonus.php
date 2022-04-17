<?php /** @var array $prize */ ?>

<tr>
    <td><?=$prize['type'];?></td>
    <td><?=$prize['bonus_amount'];?></td>
    <td></td>
    <td>
        <?php if (!$prize['admissed']): ?>
        <form id="bonus_admission_<?=$prize['id'];?>" action="/admissBonus">
            <input hidden type="number" name="id" value="<?=$prize['id'];?>">
            <input type="submit" value="Зачислить на бонусный счет">
        </form>
        <form id="bonus_reject_<?=$prize['id'];?>" action="/rejectBonus">
            <input hidden type="number" name="id" value="<?=$prize['id'];?>">
            <input type="submit" value="Отказаться от приза">
        </form>
        <?php else: ?>
        Зачислен на бонусный счет
        <?php endif; ?>
    </td>
</tr>