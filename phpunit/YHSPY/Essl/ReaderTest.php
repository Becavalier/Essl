<?php

namespace YHSPY\EsslUnit;

use PHPUnit\Framework\TestCase;
use YHSPY\Essl\Reader;
use VirtualFileSystem\FileSystem;

class ReaderTest extends TestCase
{
    /**
     * @test
     * @expectedException \YHSPY\Essl\Exception
     * @expectedExceptionCode 1001
     */
    public function testFileNotFound()
    {
        $reader = new Reader();
        $reader->readFromFile("idontexist.tstst.stst.stst");
    }

    /**
     * @test
     * @expectedException \YHSPY\Essl\Exception
     * @expectedExceptionCode 2001
     */
    public function testJunkCert()
    {
        $fs = new FileSystem();
        $fs->createFile("/junk.crt", "junk-content");

        $reader = new Reader();
        $reader->readFromFile($fs->path("/junk.crt"));
    }
}