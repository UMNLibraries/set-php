<?php

namespace UmnLib\Core;

class Set
{
    protected $members = array();
    public function members()
    {
        return array_keys( $this->members );
    }
    public function membersArray()
    {
        return array_keys( $this->members );
    }
    public function membersHash()
    {
        return $this->members;
    }
    protected function &membersHashref()
    {
        return $this->members;
    }

    public function __construct()
    {
        $args = func_get_args();
        if (!isset($args)) return;
        foreach ($args as $arg) {
            if (is_scalar($arg)) {
                $this->add( $arg );
            } else if (is_array($arg)) {
                // We're not going to recurse any further than this,
                // so call it a member at this point:
                foreach ($arg as $member) {
                    $this->add( $member );
                }
            } else {
                throw new Exception("Do not know how to process argument '$arg'");
            }
        }
    }

    public function contains( $member )
    {
        $members = $this->membersHash();
        return array_key_exists($member, $members) ? true : false;
    }

    public function find( $predicate )
    {
        // TODO: where predicate is a function that all output members must satisfy
    }

    public function add( $member )
    {
        $members = &$this->membersHashref();
        // Set this to a true value so that isset() will return true.
        $members[$member] = true;
    }

    public function delete( $member )
    {
        $members = &$this->membersHashref();
        // Set this to a true value so that isset() will return true.
        unset( $members[$member] );
    }

    public function clear()
    {
        foreach ($this->membersArray() as $member) {
            $this->delete( $member );
        }
    }

    public function size()
    {
        return count( $this->membersArray() );
    }

    public function isEmpty()
    {
        return $this->size() == 0 ? true : false;
    }

    //public function union( $set )
    public function union()
    {
        $a = $this->membersHash();
        $args = func_get_args();
        $b = $this->argMembersHash( $args );
        // Don't need array_unique with hashes:
        $class = get_class($this);
        $members = array_keys(array_merge($a, $b)); 
        return new $class( $members );
    }

    public function intersect()
    {
        $a = $this->membersHash();
        $args = func_get_args();
        $b = $this->argMembersHash( $args );
        // Don't need array_unique with hashes:
        $class = get_class($this);
        $members = array_keys(array_intersect_assoc($a, $b));
        return new $class( $members );
    }

    public function diff()
    {
        $a = $this->membersHash();
        $args = func_get_args();
        $b = $this->argMembersHash( $args );
        $class = get_class($this);
        $members = array_keys(array_diff_assoc($a, $b));
        return new $class( $members );
    }

    protected function argMembersHash( $args )
    {
        $membersHash = array();
        if (is_object($args[0]) && method_exists($args[0], 'membersHash')) {
            $membersHash = $args[0]->membersHash();
        } else if (is_array($args[0])) {
            // Here we just assume that the user passed in an array of scalars.
            // TODO: Do more validation?
            foreach ($args[0] as $member) {
                $membersHash[$member] = true;
            }
        } else {
            // Here we just assume that the user passed in an array of scalars.
            // TODO: Do more validation?
            foreach ($args as $member) {
                $membersHash[$member] = true;
            }
        }
        return $membersHash;
    }
}
