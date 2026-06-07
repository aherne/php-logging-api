<?php

namespace Lucinda\Logging\Driver\Json;

use Lucinda\Logging\RequestInformation;

/**
 * Logs messages/errors as newline-delimited JSON objects.
 */
class Logger extends \Lucinda\Logging\Logger
{
    public const EXTENSION = "json";

    private string $filePath;
    private string $rotationPattern;
    private RequestInformation $requestInformation;

    /**
     * Creates logger instance.
     *
     * @param string             $filePath           Log file (without extension) and its absolute path.
     * @param RequestInformation $requestInformation Request information to add to each log line.
     * @param string             $rotationPattern    PHP date function format by which logs will rotate.
     */
    public function __construct(string $filePath, RequestInformation $requestInformation, string $rotationPattern="")
    {
        $this->filePath = $filePath;
        $this->rotationPattern = $rotationPattern;
        $this->requestInformation = $requestInformation;
    }

    /**
     * Performs the act of logging.
     *
     * @param string|\Throwable $info  Information that needs being logged
     * @param integer           $level Log level (see: https://tools.ietf.org/html/rfc5424)
     */
    protected function log(string|\Throwable $info, int $level): void
    {
        $fileName = $this->filePath.($this->rotationPattern ? "__".date($this->rotationPattern) : "").".".self::EXTENSION;
        file_put_contents($fileName, json_encode($this->getPayload($info, $level), JSON_UNESCAPED_SLASHES)."\n", FILE_APPEND | LOCK_EX);
    }

    /**
     * Gets payload to be encoded as JSON.
     *
     * @param  string|\Throwable $info  Information that needs being logged
     * @param  integer           $level Log level (see: https://tools.ietf.org/html/rfc5424)
     * @return array<string,mixed>
     */
    private function getPayload(string|\Throwable $info, int $level): array
    {
        $payload = [
            "timestamp" => date("Y-m-d H:i:s"),
            "level" => $level,
            "message" => $info instanceof \Throwable ? $info->getMessage() : $info,
        ];

        if ($info instanceof \Throwable) {
            $payload["exception"] = [
                "class" => get_class($info),
                "file" => $info->getFile(),
                "line" => $info->getLine(),
                "trace" => $info->getTraceAsString(),
            ];
        }

        if ($uri = $this->requestInformation->getUri()) {
            $payload["uri"] = $uri;
        }
        if ($ipAddress = $this->requestInformation->getIpAddress()) {
            $payload["ip_address"] = $ipAddress;
        }
        if ($userAgent = $this->requestInformation->getUserAgent()) {
            $payload["user_agent"] = $userAgent;
        }

        return $payload;
    }
}
