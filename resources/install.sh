FILENAME="$2/dependancy_kkasa_in_progress"
REQ_VERSION="2.3.10"
echo "Destination is $1"
echo "Tmp folder is $2"
touch ${FILENAME}
echo "Launch install of KKasa dependancy"
echo "* KKPA"
echo 0 > ${FILENAME}
cd $2
echo 10  > ${FILENAME}
rm -f v2.zip
echo 20  > ${FILENAME}
wget https://github.com/kavod/KKPA/archive/V${REQ_VERSION}.zip
echo 30 > ${FILENAME}
unzip -o "V${REQ_VERSION}.zip"
echo 40 > ${FILENAME}
rm -f "V${REQ_VERSION}.zip"
echo 50 > ${FILENAME}
rm -Rf "$1/KKPA"
echo 60 > ${FILENAME}
mkdir "$1/KKPA"
echo 70 > ${FILENAME}
mv -f KKPA-${REQ_VERSION}/src/* "$1/KKPA"
echo 90 > ${FILENAME}
rm -Rf KKPA-${REQ_VERSION}
echo 100 > ${FILENAME}
echo "Everything is successfully installed!"
rm ${FILENAME}
