---
id: performance-measurement
title: Performance measurement
sidebar_position: 7
---

## Flame graphs

The LanSuite developer setup supports generation of [xDebug traces](https://xdebug.org/docs/trace).
Those traces can be used to generate a [flame graph](https://www.brendangregg.com/flamegraphs.html).

This guide shows you how to generate a flame graph.
It assumes you have a local development setup running.
This guide is not meant for your production site.

1. Get a local copy of [https://github.com/brendangregg/FlameGraph](https://github.com/brendangregg/FlameGraph)

2. Start LanSuite via the docker-compose setup:
    ```
    docker-compose up
    ```

3. Run a website call with the GET parameter `?XDEBUG_TRACE=lansuite` like `http://127.0.0.1:8080/?XDEBUG_TRACE=lansuite`

4. A new trace is generated and stored inside your root directory of the source code.
   It is called like `xdebug.trace.1689363817._code_index_php.xt.gz`

5. Unpack the trace
    ```
    gunzip xdebug.trace.1689363817._code_index_php.xt.gz
    ```

6. Switch to your local copy of the FlameGraph repository and call
   ```
   php stackcollapse-xdebug.php ../lansuite/xdebug.trace.1689363817._code_index_php.xt | ./flamegraph.pl > lansuite.svg
   ```

7. Open `lansuite.svg` and you should see something like

    <img src="/lansuite/img/flamegraph/lansuite.svg" />