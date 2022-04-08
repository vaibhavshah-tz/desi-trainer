<?php

use Illuminate\Database\Seeder;

use App\Models\TicketType;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ticketList = [
            [
                'name' => 'Training',
                'image' => 'test',
            ],
            [
                'name' => 'Job Support',
                'image' => 'test',
            ],
            [
                'name' => 'Interview Support',
                'image' => 'test',
            ]
        ];
        TicketType::truncate();
        TicketType::insert($ticketList);
    }
}
