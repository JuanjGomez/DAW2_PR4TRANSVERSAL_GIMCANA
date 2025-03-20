<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'History',
            'Culture',
            'Architecture',
            'Art',
            'Food',
            'Shopping',
            'Entertainment',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
            ]);
        }
    }
}
