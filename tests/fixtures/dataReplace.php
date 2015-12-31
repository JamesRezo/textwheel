<?php

function dataReplace()
{
    $data = array();

    $data['Fallback test'] = array(
        'This is a simple test written by myself',
        array('replace' => '', 'type' => 'unknown')
    );

    $data['All with $0'] = array(
        '-- ' . 'This is a simple test written by myself' . ' --',
        array('replace' => '-- $0 --', 'type' => 'all')
    );

    $data['All without $0'] = array(
        'replace',
        array('replace' => 'replace', 'type' => 'all')
    );

    $data['str_replace test 1'] = array(
        'This is a simple test I wrote by myself',
        array('type' => 'str', 'replace' => 'I wrote', 'match' => 'written')
    );

    $data['str_replace test 2'] = array(
        'This is a simple test written to myself',
        array('type' => 'str', 'replace' => 'to', 'match' => 'by')
    );

    $data['strtr test 1'] = array(
        'This#is#a#simple#test#written#by#myself',
        array('type' => 'str', 'replace' => array('#'), 'match' => array(' '))
    );

    $data['strtr test 2'] = array(
        'This is a simple test written to moself',
        array('type' => 'str', 'replace' => array('t', 'o'), 'match' => array('b', 'y'))
    );

    $data['preg replacement 1'] = array(
        'This is a simple test I wrote by myself',
        array('replace' => 'I wrote', 'match' => '/written/')
    );

    $data['preg replacement 2'] = array(
        'This is a simple test I wrote to myself',
        array('replace' => array('to', 'I wrote'), 'match' => array('/by/', '/written/'))
    );

    return $data;
}
