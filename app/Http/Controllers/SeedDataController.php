<?php

namespace App\Http\Controllers;

use App\ClassSubject;
use App\ClassX;
use App\Draft;
use App\Subject;
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

	public function seedSubject() {
		$drafts = Draft::all();

		foreach ( $drafts as $index => $draft ) {
			$maMH = $draft->maMH;

			$subjects = Subject::all()->where( 'maMH', $maMH );
			if ( $subjects->count() == 0 ) {
				$s = Subject::create( [
					'name'  => $draft->tenMH,
					'maMH'  => $draft->maMH,
					'soTin' => $draft->soTin,
				] );

				var_dump( $s );
			}
		}
	}

	public function seedClassSubject() {
		$drafts = Draft::all();

		foreach ( $drafts as $index => $draft ) {
			$maMH  = $draft->maMH;
			$maLMH = $draft->maLMH;

			$classSubjects = ClassSubject::all()->where( 'maLMH', $maLMH );

			$subject = Subject::all()->where( 'maMH', $maMH )->first();

			$subject_id = $subject->id;
			if ( $classSubjects->count() == 0 ) {
				$c = ClassSubject::create( [
					'maLMH'   => $maLMH,
					'subject' => $subject_id,
				] );

				var_dump( $c );
			}
		}
	}
}
