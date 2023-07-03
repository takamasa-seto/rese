#!/bin/bash
mkdir ./src/storage/app/public/rese
mkdir ./src/storage/app/public/rese/image
curl -OL https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/sushi.jpg
mv sushi.jpg ./src/storage/app/public/rese/image/
curl -OL https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/yakiniku.jpg
mv yakiniku.jpg ./src/storage/app/public/rese/image/
curl -OL https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/izakaya.jpg
mv izakaya.jpg ./src/storage/app/public/rese/image/
curl -OL https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/italian.jpg
mv italian.jpg ./src/storage/app/public/rese/image/
curl -OL https://coachtech-matter.s3-ap-northeast-1.amazonaws.com/image/ramen.jpg
mv ramen.jpg ./src/storage/app/public/rese/image/
