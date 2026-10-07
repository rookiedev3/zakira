<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rapikan home_order lama (0 / kembar / berlubang) menjadi 1..n.
     */
    public function up(): void
    {
        $i = 1;

        DB::table('brands')
            ->orderBy('home_order')
            ->orderBy('id')
            ->pluck('id')
            ->each(function ($id) use (&$i) {
                DB::table('brands')->where('id', $id)->update(['home_order' => $i++]);
            });
    }

    public function down(): void
    {
        // Tidak ada yang perlu dikembalikan.
    }
};