<?php

namespace App\Http\Controllers;

use App\ClassX;
use App\Draft;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

class SeedDataController extends Controller {
	/**
	 * Init data classX
	 */
	public function seedDataClassX_es() {
		$ks = [
			'K60',
			'K59',
			'K58',
			'K57',
		];

		$ns = [
			'CA',
			'CAC',
			'CB',
			'CC',
			'CD',
			'CLC',
			'T',
			'N',
			'ĐA',
			'ĐB',
			'M',
			'V',
			'H'
		];

		foreach ( $ks as $i => $k ) {
			foreach ( $ns as $j => $n ) {
				$class_name = $k . $n;

				$class = ClassX::all()->where( 'name', $class_name );
				if ( $class->count() == 0 ) {
					$cl = ClassX::create( [
						'khoa' => $k,
						'lop'  => $n,
					] );
				}
			}
		}
	}

	public function seedTimetable() {
		$contents = Storage::disk( 'local' )->get( 'tkb.json' );

		$obj = json_decode( $contents );
		foreach ( $obj as $o ) {
			$draf = Draft::create( [
				'maMH'    => ( trim( $o->maMH ) ),
				'tenMH'   => ( trim( $o->tenMH ) ),
				'soTin'   => ( trim( $o->soTin ) ),
				'maLMH'   => ( trim( $o->maLMH ) ),
				'teacher' => ( trim( $o->teacher ) ),
				'soSV'    => ( trim( $o->soSV ) ),
				'thu'     => ( trim( $o->thu ) ),
				'tiet'    => ( trim( $o->tiet ) ),
				'address' => ( trim( $o->address ) ),
				'note'    => ( trim( $o->note ) ),
			] );

			var_dump( $draf );
		}
	}
}
