<?php /** @var array $prize */ ?>

<tr>
    <td><?=$prize['type'];?></td>
    <td></td>
    <td><?=$prize['name']?></td>
    <td>
        <?php if (!$prize['shipped']): ?>
        <form id="thing_shipping_<?=$prize['id'];?>" action="/sendThing">
            <input hidden type="number" name="id" value="<?=$prize['id'];?>">
            <input type="submit" value="Отправить по почте">
        </form>
        <form id="thing_reject_<?=$prize['id'];?>" action="/rejectThing">
            <input hidden type="number" name="id" value="<?=$prize['id'];?>">
            <input type="submit" value="Отказаться от приза">
        </form>
        <?php else: ?>
        Подарок отправлен по почте
        <?php endif; ?>
    </td>
</tr>