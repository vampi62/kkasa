FILENAME="$2/dependancy_kkasa_in_progress"
echo "Destination is $1"
echo "Tmp folder is $2"
touch ${FILENAME}
echo "Launch install of KKasa dependancy"
echo 0 > ${FILENAME}
cd $2
echo 10  > ${FILENAME}
rm -f master.zip
echo 20  > ${FILENAME}
wget https://github.com/kavod/KKPA/archive/master.zip
echo 30 > ${FILENAME}
unzip -o master.zip
echo 40 > ${FILENAME}
rm -f master.zip
echo 50 > ${FILENAME}
rm -Rf "$1/KKPA"
echo 60 > ${FILENAME}
mkdir "$1/KKPA"
echo 80 > ${FILENAME}
mv -f KKPA-master/src/* "$1/KKPA"
echo 90 > ${FILENAME}
rm -Rf KKPA-master
echo 100 > ${FILENAME}
echo "Everything is successfully installed!"
rm ${FILENAME}
