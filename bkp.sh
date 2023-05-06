
zip=bkp.zip
[[ -f $zip ]] && rm $zip

for d in db/{1400x1400,1280x720}
do
    for f in $(ls $d -t | head -n 300)
    do
        zip $zip $d/$f
    done
done

gh release upload bkp bkp.zip
