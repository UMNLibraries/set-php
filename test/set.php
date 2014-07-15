#!/usr/bin/php -q
<?php

require_once 'simpletest/autorun.php';
SimpleTest :: prefer(new TextReporter());
set_include_path('../php' . PATH_SEPARATOR . get_include_path());
require_once 'Set.php';

ini_set('memory_limit', '2G');

//error_reporting( E_STRICT );
error_reporting( E_ALL );

class SetTest extends UnitTestCase
{
    public function __construct()
    {
    }

    public function test_new()
    {
        $this->empty_set = new Set();
        $this->assertIsA( $this->empty_set, 'Set' );
        $this->assertEqual( $this->empty_set->members(), array() );
        $this->assertEqual( $this->empty_set->size(), 0 );

        $members = array('foo','bar','baz');
        $this->set = new Set( $members );
        $this->assertIsA( $this->set, 'Set' );
        $this->assertEqual($this->set->members(), $members);
        $this->assertEqual( $this->set->size(), 3 );
    }

    public function test_is_empty()
    {
        $this->assertTrue( $this->empty_set->is_empty() );
        $this->assertFalse( $this->set->is_empty() );
    }

    public function test_contains()
    {
        $this->assertFalse( $this->empty_set->contains('foo') );
        $this->assertTrue( $this->set->contains('foo') );
        $this->assertFalse( $this->set->contains('fu') );
    }

    public function test_add_delete()
    {
        $this->set->add('fu');
        $this->assertTrue( $this->set->contains('fu') );
        $members = array('foo','bar','baz','fu');
        $this->assertEqual( $this->set->members(), $members );
        $this->assertEqual( $this->set->size(), 4 );

        $this->set->delete('fu');
        array_pop( $members );
        $this->assertFalse( $this->set->contains('fu') );
        $this->assertEqual( $this->set->members(), $members );
        $this->assertEqual( $this->set->size(), 3 );
    }

    public function test_clear()
    {
        $set = new Set('fee','fye','foe','fum');
        $this->assertEqual( $set->size(), 4 );

        $set->clear();
        $this->assertEqual( $set->members(), array() );
        $this->assertEqual( $set->size(), 0 );
        $this->assertTrue( $set->is_empty() );
    }

    public function test_union()
    {
        // Disjoint sets:
        $a = new Set('fee','fye','foe','fum');
        $b = new Set('hickory','dickory','dock');
        $a_union_b = $a->union( $b );
        $this->assertEqual(
            $a_union_b->members(),
            array('fee','fye','foe','fum','hickory','dickory','dock')
        );
        $this->assertEqual(
            $a_union_b->members_hash(),
            array(
             'fee' => true,
             'fye' => true,
             'foe' => true,
             'fum' => true,
             'hickory' => true,
             'dickory' => true,
             'dock' => true,
            )
        );

        // Intersecting sets:
        $c = new Set('kung','foo');
        $c_union_d = $c->union('foo','manchu');
        $this->assertEqual(
            $c_union_d->members(),
            array('kung','foo','manchu')
        );
        $this->assertEqual(
            $c_union_d->members_hash(),
            array(
             'kung' => true,
             'foo' => true,
             'manchu' => true,
            )
        );
    }

    public function test_intersect()
    {
        // Disjoint sets:
        $a = new Set('fee','fye','foe','fum');
        $b = new Set('hickory','dickory','dock');
        $a_intersect_b = $a->intersect( $b );
        $this->assertEqual(
            $a_intersect_b->members(),
            $this->empty_set->members()
        );
        $this->assertEqual(
            $a_intersect_b->members_hash(),
            $this->empty_set->members_hash()
        );

        // Intersecting sets:
        $c = new Set('kung','foo');
        $c_intersect_d = $c->intersect('foo','manchu');
        $this->assertEqual(
            $c_intersect_d->members(),
            array('foo')
        );
        $this->assertEqual(
            $c_intersect_d->members_hash(),
            array(
             'foo' => true,
            )
        );
    }

    public function test_diff()
    {
        // Disjoint sets:
        $a_members = array('fee','fye','foe','fum');
        $b_members = array('hickory','dickory','dock');
        $a = new Set( $a_members );
        $b = new Set( $b_members );
        $a_diff_b = $a->diff( $b );
        $this->assertEqual(
            $a_diff_b->members(),
            $a_members
        );
        $this->assertEqual(
            $a_diff_b->members_hash(),
            array(
             'fee' => true,
             'fye' => true,
             'foe' => true,
             'fum' => true,
            )
        );

        // Intersecting sets:
        $c = new Set('kung','foo' );
        $c_diff_d = $c->diff('foo','manchu');
        $this->assertEqual(
            $c_diff_d->members(),
            array('kung')
        );
        $this->assertEqual(
            $c_diff_d->members_hash(),
            array(
             'kung' => true,
            )
        );
    }

} // end class SetTest
