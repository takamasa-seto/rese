<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Exception;
use App\Models\Review;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate()
    {
        Review::create([
            'user_id'=>'1',
            'shop_id'=>'2',
            'star'=>'3',
            'comment'=>'あいうえお',
            'image_url'=>'https://abc.com'
        ]);
        $this->assertDatabaseHas('reviews',[
            'user_id'=>'1',
            'shop_id'=>'2',
            'star'=>'3',
            'comment'=>'あいうえお',
            'image_url'=>'https://abc.com'
        ]);

        Review::create([
            'user_id'=>'1',
            'shop_id'=>'3',
            'star'=>'4',
            'comment'=>'かきくけこ',
            'image_url'=>'https://abc.co.jp'
        ]);
        $this->assertDatabaseHas('reviews',[
            'user_id'=>'1',
            'shop_id'=>'3',
            'star'=>'4',
            'comment'=>'かきくけこ',
            'image_url'=>'https://abc.co.jp'
        ]);
        Review::create([
            'user_id'=>'2',
            'shop_id'=>'2',
            'star'=>'4',
            'comment'=>'かきくけこ',
            'image_url'=>'https://abc.co.jp'
        ]);
        $this->assertDatabaseHas('reviews',[
            'user_id'=>'2',
            'shop_id'=>'2',
            'star'=>'4',
            'comment'=>'かきくけこ',
            'image_url'=>'https://abc.co.jp'
        ]);

        //複合ユニーク制約でエラーとなる場合
        $this->expectException(Exception::class);
        Review::create([
            'user_id'=>'1',
            'shop_id'=>'2',
            'star'=>'4',
            'comment'=>'かきくけこ',
            'image_url'=>'https://abc.co.jp'
        ]);
        
    }
}