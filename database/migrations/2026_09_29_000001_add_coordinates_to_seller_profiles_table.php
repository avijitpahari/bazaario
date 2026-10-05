<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('seller_profiles', 'latitude')) {
            Schema::table('seller_profiles', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('country');
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }

        // Populate known city coordinates and default coordinates if null
        $cityCoordinates = [
            'kolkata'   => [22.572646, 88.363895],
            'bengaluru' => [12.971599, 77.594566],
            'bangalore' => [12.971599, 77.594566],
            'jaipur'    => [26.912434, 75.787270],
            'varanasi'  => [25.317645, 82.973915],
            'contai'    => [21.778124, 87.751624],
            'mumbai'    => [19.076090, 72.877426],
            'delhi'     => [28.704060, 77.102493],
            'digha'     => [21.626600, 87.507400],
        ];

        foreach ($cityCoordinates as $city => $coords) {
            DB::table('seller_profiles')
                ->whereRaw('LOWER(city) = ?', [$city])
                ->where(function ($q) {
                    $q->whereNull('latitude')->orWhereNull('longitude');
                })
                ->update([
                    'latitude'  => $coords[0],
                    'longitude' => $coords[1],
                ]);
        }

        // Set default Kolkata coordinates for any remaining seller profiles with null coordinates
        DB::table('seller_profiles')
            ->whereNull('latitude')
            ->update([
                'latitude'  => 22.572646,
                'longitude' => 88.363895,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('seller_profiles', 'latitude')) {
            Schema::table('seller_profiles', function (Blueprint $table) {
                $table->dropColumn(['latitude', 'longitude']);
            });
        }
    }
};
