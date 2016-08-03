<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findExtendedClassName method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;

class FindExtendedClassNameTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The PHP_CodeSniffer_File object containing parsed contents of this file.
     *
     * @var PHP_CodeSniffer_File
     */
    private $phpcsFile;


    /**
     * Initialize & tokenize PHP_CodeSniffer_File with code from this file.
     *
     * Methods used for these tests can be found at the bottom of
     * this file.
     *
     * @return void
     */
    public function setUp()
    {
        $config            = new Config();
        $config->standards = array('Generic');
        $config->sniffs    = array('Generic.None.None');

        $ruleset = new Ruleset($config);

        $this->phpcsFile = new DummyFile(file_get_contents(__FILE__), $ruleset, $config);
        $this->phpcsFile->process();

    }//end setUp()


    /**
     * Clean up after finished test.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->phpcsFile);

    }//end tearDown()


    /**
     * Test a class that extends another.
     *
     * @return void
     */
    public function testExtendedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testExtendedClass */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertSame('testFECNClass', $found);

    }//end testExtendedClass()


    /**
     * Test a class that extends another, using namespaces.
     *
     * @return void
     */
    public function testNamespacedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNamespacedClass */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertSame('\PHP_CodeSniffer\Tests\Core\File\testFECNClass', $found);

    }//end testNamespacedClass()


    /**
     * Test a class that doesn't extend another.
     *
     * @return void
     */
    public function testNonExtendedClass()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testNonExtendedClass */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertFalse($found);

    }//end testNonExtendedClass()


    /**
     * Test an interface.
     *
     * @return void
     */
    public function testInterface()
    {
        $start = ($this->phpcsFile->numTokens - 1);
        $class = $this->phpcsFile->findPrevious(
            T_COMMENT,
            $start,
            null,
            false,
            '/* testInterface */'
        );

        $found = $this->phpcsFile->findExtendedClassName(($class + 2));
        $this->assertFalse($found);

    }//end testInterface()


}//end class

// @codingStandardsIgnoreStart
class testFECNClass {}
/* testExtendedClass */ class testFECNExtendedClass extends testFECNClass {}
/* testNamespacedClass */ class testFECNNamespacedClass extends \PHP_CodeSniffer\Tests\Core\File\testFECNClass {}
/* testNonExtendedClass */ class testFECNNonExtendedClass {}
/* testInterface */ interface testFECNInterface {}
// @codingStandardsIgnoreEnd
