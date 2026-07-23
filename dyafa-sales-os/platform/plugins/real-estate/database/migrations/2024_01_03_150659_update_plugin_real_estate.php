<?php

use Botble\RealEstate\Enums\PropertyTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Botble\RealEstate\Models\Review;
use Botble\RealEstate\Enums\ReviewStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('re_properties', 'type')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->string('type', 60)->default(PropertyTypeEnum::SALE);
            });
            $properties = DB::table('re_properties')->get();
            foreach ($properties as $property) {
                DB::table('re_properties')
                    ->where('id', $property->id)
                    ->update([
                        'type' => $property->id == 1 ? PropertyTypeEnum::SALE : PropertyTypeEnum::RENT,
                    ]);
            }
        }
        if (Schema::hasColumn('re_properties', 'type_id')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->integer('type_id')->nullable()->change();
            });
        }

        if (!Schema::hasColumn('re_properties', 'status')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->string('status', 60)->default('selling');
            });
        }

        if (!Schema::hasColumn('re_properties', 'views')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->integer('views')->unsigned()->default(0);
            });
        }

        if (!Schema::hasColumn('re_projects', 'views')) {
            Schema::table('re_projects', function (Blueprint $table) {
                $table->integer('views')->unsigned()->default(0);
            });
        }

        if (Schema::hasColumn('re_reviews', 'comment')) {
            Schema::table('re_reviews', function (Blueprint $table) {
                $table->renameColumn('comment', 'content');
            });
        }

        DB::table('re_reviews')
            ->where('status', 'published')
            ->update(['status' => ReviewStatusEnum::APPROVED]);

        $reviews = Review::get();

        foreach ($reviews as $review) {
            $reviews_meta = DB::table('re_reviews_meta')
                ->where('review_id', $review->id)
                ->get();
            $meta = [];
            foreach ($reviews_meta as $key => $item) {
                $meta[$item->key]  = $item->value;
            }
            save_meta_data($review, 'review_meta', json_encode($meta));
            calculateReviewMetaData($meta, $review->reviewable_id);
        }


        if (!Schema::hasColumn('re_consults', 'project_id')) {
            Schema::table('re_consults', function (Blueprint $table) {
                $table->foreignId('project_id')->nullable();
            });
        }

        if (!Schema::hasColumn('re_properties', 'views')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->integer('views')->unsigned()->default(0);
            });
        }

        if (!Schema::hasColumn('re_projects', 'views')) {
            Schema::table('re_projects', function (Blueprint $table) {
                $table->integer('views')->unsigned()->default(0);
            });
        }

        Schema::table('re_projects', function (Blueprint $table) {
            $table->text('images')->nullable()->change();
        });

        Schema::table('re_properties', function (Blueprint $table) {
            $table->text('images')->nullable()->change();
        });

        if (Schema::hasTable('re_transactions')) {
            Schema::rename('re_transactions', 'transactions');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('re_properties', 'type')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn('re_properties', 'type_id')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->integer('type_id')->unsigned()->nullable(false)->change();
            });
        }

        if (Schema::hasColumn('re_properties', 'status')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('re_properties', 'views')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }

        if (Schema::hasColumn('re_projects', 'views')) {
            Schema::table('re_projects', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }

        if (Schema::hasColumn('re_reviews', 'content')) {
            Schema::table('re_reviews', function (Blueprint $table) {
                $table->renameColumn('content', 'comment');
            });
        }

        if (Schema::hasColumn('re_consults', 'project_id')) {
            Schema::table('re_consults', function (Blueprint $table) {
                $table->dropColumn('project_id');
            });
        }

        if (Schema::hasColumn('re_properties', 'views')) {
            Schema::table('re_properties', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }

        if (Schema::hasColumn('re_projects', 'views')) {
            Schema::table('re_projects', function (Blueprint $table) {
                $table->dropColumn('views');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::rename('transactions', 're_transactions');
        }
    }
};
