#!/bin/bash
ps aux|grep php|grep -v grep|awk '{print $2}'|xargs kill -9
echo 'kill complete';
