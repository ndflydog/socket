<?php
include __DIR__."/server.php";
#循环监听客户端
class SelectServerSocket extends ServerSocket
{
    public function run()
    {
        $this->loop();
    }

    protected function reply()
    {
        echo 'reply调用'.PHP_EOL;
        $mxData = $this->read();
        if (!$mxData) {
            return false;
        }
        var_dump($mxData);
        $strMessage = "Client: ".trim($mxData)."\n";
        $this->write($strMessage);
    }

    public function loop()
    {
        $arrRead = [];
        $arrWrite = $arrExp = null;
        $arrClient = [$this->pSocket];

        while(true) {
            $arrRead = $arrClient;
            var_dump($arrRead);
            if (socket_select($arrRead, $arrWrite, $arrExp, null) < 1) {
                continue;
            }
            foreach ($arrRead as $pSocket) {
                if ($pSocket == $this->pSocket) {
                    $this->connect();
                    $arrClient[] = $this->pClient;
                } else {
                    $bRes = $this->reply();
                    echo 1111;
                    if ($bRes === false) {
                        $nKey = array_search($pSocket, $arrClient, true);
                        $this->close($arrClient[$nKey]);
                        unset($arrClient[$nKey]);
                        continue;
                    }
                }
            }
        }
    }
}
$strHost     = "127.0.0.1";
$nPort       = 25003;
$pServer = new SelectServerSocket($strHost, $nPort);
$pServer->run();