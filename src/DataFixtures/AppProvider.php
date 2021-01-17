<?php

namespace App\DataFixtures;

use Faker\Factory;

/**
 * Class AppProvider
 * @package App\DataFixtures
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class AppProvider
{
    public static function html(): string
    {
        $faker = Factory::create('fr_FR');

        $paragraphs = $faker->paragraphs(rand(8, 12));

        return '<p>' . implode('</p><p>', $paragraphs) . '</p>';
    }
}
