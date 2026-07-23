<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Models\Review;
use Faker\Factory;

class ReviewSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        Review::truncate();
        $properties = Property::all();
        foreach ($properties as $property) {

            foreach (Account::limit($faker->numberBetween(5, 10))->get() as $account) {
                $meta = [
                    'service' => $faker->numberBetween(1, 5),
                    'value' => $faker->numberBetween(1, 5),
                    'location' => $faker->numberBetween(1, 5),
                    'cleanliness' => $faker->numberBetween(1, 5),
                ];
                $sum = 0;

                foreach ($meta as $value) {
                    $sum += $value;
                }

                $star = $sum / count($meta);
                $review = Review::create([
                    'reviewable_id' => $property->id,
                    'reviewable_type' => Property::class,
                    'account_id' => $account->id,
                    'star' => $star,
                    'content' => $faker->text(150),
                    'status' => ReviewStatusEnum::APPROVED,
                ]);
                save_meta_data($review, 'review_meta', json_encode($meta));
                calculateReviewMetaData($meta, $review->reviewable_id);
            }
        }
    }
}
