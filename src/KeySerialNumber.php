<?php

class KeySerialNumber
{
    private $baseKeyId;
    private $trsmId;
    private $transactionCounter;
    private $paddedKsn;
    private $unpaddedKsn;
    private $initialKey;

    /**
     * Create a new instance of the KeySerialNumber class
     *
     * @param $ksn
     *            Key Serial Number to initialise with
     * @param $ksnDescriptor
     *            KSN descriptor
     */
    public function __construct($ksn, $ksnDescriptor)
    {
        $this->paddedKsn = $ksn;
        $this->unpaddedKsn = stripKsn($ksn);

        // The base key ID is the first n chars of the unpadded KSN. n is determined by the first two positions $ksn
        // descriptor
        $n = (int) substr($ksnDescriptor, 0, 1);
        $this->baseKeyId = substr($this->unpaddedKsn, 0, $n);

        // The TRSM ID is the following m chars of the unpadded $ksn. m is determined by the last position in the $ksn
        // descriptor
        $m = (int) (substr($ksnDescriptor, 2, 3));
        $this->trsmId = substr($this->unpaddedKsn, $n, $n + $m);

        // The transaction counter is a mystery to me. I don't know what on earth this code does, but I want to
        $huh = $this->unpaddedKsn[($m + $n - 1)];
        $surepal = ord($huh);
        $surepal = $surepal & 1;
        $iReallyDontUnderstandThis = chr($surepal);
        $this->transactionCounter = $iReallyDontUnderstandThis + substr($this->unpaddedKsn, (m + n));
    }

    /**
     * Strips the padding off the KSN
     *
     * @param $ksn Padded KSN
     *
     * @return Unpadded KSN
     */
    public static function stripKsn($ksn)
    {
        if (strpos($ksn, 'FFFF') === 0) {
            return substr($ksn, 4);
        }

        return $ksn;
    }

    public function getBaseKeyId()
    {
        return $this->baseKeyId;
    }

    public function getInitialKey()
    {
        return $this->initialKey;
    }

    public function getPaddedKsn()
    {
        return $this->paddedKsn;
    }

    public function getTransactionCounter()
    {
        return $this->transactionCounter;
    }

    public function getTrsmId()
    {
        return $this->trsmId;
    }

    public function getUnpaddedKsn()
    {
        return $this->unpaddedKsn;
    }

    public function setInitialKey($initialKey)
    {
        $this->initialKey = $initialKey;
    }

    public function pack()
    {
        return Utility::hex2bin($this->paddedKsn);
    }
}
