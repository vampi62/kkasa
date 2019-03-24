FILENAME="$2/dependancy_kkasa_in_progress"
echo "Destination is $1"
echo "Tmp folder is $2"
touch ${FILENAME}
echo "Launch install of KKasa dependancy"
echo "* KKPA"
echo 0 > ${FILENAME}
cd $2
echo 5  > ${FILENAME}
rm -f v2.zip
echo 10  > ${FILENAME}
wget https://github.com/kavod/KKPA/archive/v2.zip
echo 15 > ${FILENAME}
unzip -o v2.zip
echo 20 > ${FILENAME}
rm -f v2.zip
echo 25 > ${FILENAME}
rm -Rf "$1/KKPA"
echo 30 > ${FILENAME}
mkdir "$1/KKPA"
echo 35 > ${FILENAME}
mv -f KKPA-2/src/* "$1/KKPA"
echo 40 > ${FILENAME}
rm -Rf KKPA-2
echo 45 > ${FILENAME}

echo "* phpColors"
echo 50 > ${FILENAME}
cd $2
echo 55  > ${FILENAME}
rm -f master.zip
echo 60  > ${FILENAME}
wget https://github.com/mexitek/phpColors/archive/master.zip
echo 65 > ${FILENAME}
unzip -o master.zip
echo 70 > ${FILENAME}
rm -f master.zip
echo 75 > ${FILENAME}
rm -Rf "$1/phpColors"
echo 80 > ${FILENAME}
mkdir "$1/phpColors"
echo 85 > ${FILENAME}
mv -f phpColors-master/src/Mexitek/PHPColors/* "$1/phpColors"
echo 90 > ${FILENAME}
rm -Rf phpColors-master
echo 100 > ${FILENAME}
echo "Everything is successfully installed!"
rm ${FILENAME}
