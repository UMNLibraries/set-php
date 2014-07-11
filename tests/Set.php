<?php

namespace UmnLib\Core\Tests;

use UmnLib\Core\Set;

class SetTest extends \PHPUnit_Framework_TestCase
{
    public function testNew()
    {
        $emptySet = new Set();
        $this->assertInstanceOf('UmnLib\\Core\\Set', $emptySet);
        $this->assertEquals( $emptySet->members(), array() );
        $this->assertEquals( $emptySet->size(), 0 );

        $members = array('foo','bar','baz');
        $set = new Set( $members );
        $this->assertInstanceOf('UmnLib\\Core\\Set', $set);
        $this->assertEquals($set->members(), $members);
        $this->assertEquals( $set->size(), 3 );
        return array($emptySet, $set);
    }

    /**
     * @depends testNew
     */
    public function testIsEmpty(Array $sets)
    {
        list($emptySet, $set) = $sets;
        $this->assertTrue( $emptySet->isEmpty() );
        $this->assertFalse( $set->isEmpty() );
    }

    /**
     * @depends testNew
     */
    public function testContains(Array $sets)
    {
        list($emptySet, $set) = $sets;
        $this->assertFalse( $emptySet->contains('foo') );
        $this->assertTrue( $set->contains('foo') );
        $this->assertFalse( $set->contains('fu') );
    }

    /**
     * @depends testNew
     */
    public function testAddDelete(Array $sets)
    {
        list($emptySet, $set) = $sets;
        $set->add('fu');
        $this->assertTrue( $set->contains('fu') );
        $members = array('foo','bar','baz','fu');
        $this->assertEquals($members, $set->members());
        $this->assertEquals(4, $set->size());

        $set->delete('fu');
        array_pop($members);
        $this->assertFalse($set->contains('fu'));
        $this->assertEquals($members, $set->members());
        $this->assertEquals(3, $set->size());
    }

    /**
     * @depends testNew
     */
    public function test_clear(Array $sets)
    {
        list($emptySet, $set) = $sets;
        $set = new Set('fee','fye','foe','fum');
        $this->assertEquals(4, $set->size());

        $set->clear();
        $this->assertEmpty($set->members());
        $this->assertEquals(0, $set->size());
        $this->assertTrue($set->isEmpty());
    }

    public function testUnion()
    {
        // Disjoint sets:
        $a = new Set('fee','fye','foe','fum');
        $b = new Set('hickory','dickory','dock');
        $aUnionB = $a->union( $b );
        $this->assertEquals(
            array('fee','fye','foe','fum','hickory','dickory','dock'),
            $aUnionB->members()
        );
        $this->assertEquals(
            array(
             'fee' => true,
             'fye' => true,
             'foe' => true,
             'fum' => true,
             'hickory' => true,
             'dickory' => true,
             'dock' => true,
            ),
            $aUnionB->membersHash()
        );

        // Intersecting sets:
        $c = new Set('kung','foo');
        $cUnionD = $c->union('foo','manchu');
        $this->assertEquals(
            array('kung','foo','manchu'),
            $cUnionD->members()
        );
        $this->assertEquals(
            array(
             'kung' => true,
             'foo' => true,
             'manchu' => true,
            ),
            $cUnionD->membersHash()
        );
    }

    public function testIntersect()
    {
        // Disjoint sets:
        $a = new Set('fee','fye','foe','fum');
        $b = new Set('hickory','dickory','dock');
        $aIntersectB = $a->intersect( $b );
        $this->assertEmpty($aIntersectB->members());
        /*
        $this->assertEquals(
            $aIntersectB->membersHash(),
            $emptySet->membersHash()
        );
         */
        $this->assertEmpty($aIntersectB->membersHash());

        // Intersecting sets:
        $c = new Set('kung','foo');
        $cIntersectD = $c->intersect('foo','manchu');
        $this->assertEquals(
            array('foo'),
            $cIntersectD->members()
        );
        $this->assertEquals(
            array('foo' => true),
            $cIntersectD->membersHash()
        );
    }

    public function testDiff()
    {
        // Disjoint sets:
        $aMembers = array('fee','fye','foe','fum');
        $bMembers = array('hickory','dickory','dock');
        $a = new Set( $aMembers );
        $b = new Set( $bMembers );
        $aDiffB = $a->diff( $b );
        $this->assertEquals(
            $aMembers,
            $aDiffB->members()
        );
        $this->assertEquals(
            array(
             'fee' => true,
             'fye' => true,
             'foe' => true,
             'fum' => true,
            ),
            $aDiffB->membersHash()
        );

        // Intersecting sets:
        $c = new Set('kung','foo' );
        $cDiffD = $c->diff('foo','manchu');
        $this->assertEquals(
            array('kung'),
            $cDiffD->members()
        );
        $this->assertEquals(
            array('kung' => true),
            $cDiffD->membersHash()
        );
    }
}
