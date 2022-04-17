<?php

namespace Helper;

use JetBrains\PhpStorm\Pure;
use Pavelkrauchuk\Testtask\Entity\MoneyPrize;
use Pavelkrauchuk\Testtask\Helper\MoneyHelper;
use PHPUnit\Framework\TestCase;

class MoneyHelperTest extends TestCase
{
    /**
     * @dataProvider moneyPrizeDataProvider
     * @test
     * @return void
     */
    public function testConvertToBonus(MoneyPrize $moneyPrize, int $expected)
    {
        $convertedValue = MoneyHelper::convertToBonus($moneyPrize);

        $this->assertSame($expected, $convertedValue);
        $this->assertIsNumeric($convertedValue);
        $this->assertIsInt($convertedValue);
    }

    /**
     * @return array
     */
    #[Pure]
    public function moneyPrizeDataProvider(): array
    {
        return array(
            array(new MoneyPrize(null, 124), 232),
            array(new MoneyPrize(null, 987), 1846),
            array(new MoneyPrize(null, 23), 43),
            array(new MoneyPrize(null, 3698), 6915),
            array(new MoneyPrize(null, 0), 0),
        );
    }
}
