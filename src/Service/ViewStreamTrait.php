<?php
namespace Tuum\Respond\Service;

use RuntimeException;
use Tuum\Form\DataView;

trait ViewStreamTrait
{
    /**
     * @var string
     */
    protected $view_file;

    /**
     * @var array
     */
    protected $view_data = [];

    /**
     * @var null|resource
     */
    protected $fp = null;

    /**
     * @return string
     */
    abstract protected function render();

    /**
     * @return resource
     */
    protected function getResource()
    {
        if (is_null($this->fp)) {
            $this->fp = fopen('php://temp', 'wb+');
            fwrite($this->fp, $this->render());
        }

        return $this->fp;
    }

    /**
     * @param ViewData $data
     * @return DataView
     */
    protected function forgeDataView(ViewData $data)
    {
        $view = new DataView();
        $view->setData($data->getData());
        $view->setErrors($data->getInputErrors());
        $view->setInputs($data->getInputData());
        $view->setMessage($data->getMessages());

        return $view;
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try {

            return $this->getContents();

        } catch (RuntimeException $e) {
            return '';
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp        = null;
        $this->view_file = null;
        $this->view_data = [];
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $fp              = $this->getResource();
        $this->fp        = null;
        $this->view_file = null;
        $this->view_data = [];

        return $fp;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        $fp    = $this->getResource();
        $stats = fstat($fp);

        return $stats['size'];
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws RuntimeException on error.
     */
    public function tell()
    {
        $fp     = $this->getResource();
        $result = ftell($fp);
        if (!is_int($result)) {
            throw new RuntimeException('Error occurred during tell operation');
        }

        return $result;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        $fp = $this->getResource();

        return feof($fp);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *                    based on the seek offset. Valid values are identical to the built-in
     *                    PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *                    offset bytes SEEK_CUR: Set position to current location plus offset
     *                    SEEK_END: Set position to end-of-stream plus offset.
     * @return bool
     * @throws RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $fp     = $this->getResource();
        $result = fseek($fp, $offset, $whence);
        if (0 !== $result) {
            throw new RuntimeException('Error seeking within stream');
        }

        return true;
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see  seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws RuntimeException on failure.
     */
    public function rewind()
    {
        return $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return true;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws RuntimeException on failure.
     */
    public function write($string)
    {
        $written = fwrite($this->getResource(), $string);
        if (false === $written) {
            throw new RuntimeException('Error writing to stream');
        }

        return $written;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *                    them. Fewer than $length bytes may be returned if underlying stream
     *                    call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *                    if no bytes are available.
     * @throws RuntimeException if an error occurs.
     */
    public function read($length)
    {
        $read = fread($this->getResource(), $length);

        if ($read !== false) {
            return $read;
        }
        throw new RuntimeException('Error reading stream');
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        $fp = $this->getResource();
        rewind($fp);
        $contents = stream_get_contents($fp);
        if ($contents === false) {
            throw new RuntimeException('Error reading from stream');
        }

        return $contents;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *                    provided. Returns a specific key value if a key is provided and the
     *                    value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $fp       = $this->getResource();
        $metadata = stream_get_meta_data($fp);
        if (is_null($key)) {
            return $metadata;
        }

        return array_key_exists($key, $metadata) ? $metadata[$key] : null;
    }
}