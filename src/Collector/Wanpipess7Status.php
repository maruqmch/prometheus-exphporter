<?php

namespace Arrakis\Exphporter\Collector;

use TweedeGolf\PrometheusClient\CollectorRegistry;

class Wanpipess7Status extends AbstractCollector
{
    public function collect(CollectorRegistry $registry)
    {
        $registry->createGauge(
            'wanrouter_ss7_status',
            array_keys($this->getGaugeLabels(null, null)),
            null,
            null,
            CollectorRegistry::DEFAULT_STORAGE,
            true
        );
        // Return code (0 = success, >0 = error)
        $registry->createGauge(
            'command_line_count_return_code',
            array_keys($this->getGaugeLabels(null, null)),
            null,
            null,
            CollectorRegistry::DEFAULT_STORAGE,
            true
        );

        foreach ($this->config['commands'] ?? [] as $commandConfig) {
            if (empty($commandConfig['command'])) {
                $this->log('CommandLineCount: Missing command. Ignoring.', 'ERROR');
                continue;
            }
            $commandConfig += ['name' => ''];

            exec(
                $commandConfig['command'],
                $output,
                $rc
            );

            if($rc==1)
            $rc=0;
            elseif($rc==0)
            $rc=1;

            $registry->getGauge('wanrouter_ss7_status')
                ->set($rc, $this->getGaugeLabels($commandConfig['name'], $commandConfig['command']));
           if ($rc && !$output)
 
            if ($rc && empty($commandConfig['ignore_errors'])) {
                throw new Exception('The command returned an error code: ' . $rc);
            }
        }
    }

    protected function getGaugeLabels($name, $path) {
        return $this->getCommonLabels() + ['name' => $name, 'command' => $path];
    }
}
