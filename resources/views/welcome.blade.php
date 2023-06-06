<x-guest-layout>
    <div class="h-screen pb-14 bg-right bg-cover">
        <!-- ここに新たにflexを追加しました -->
        <div class="container pt-10 md:pt-18 px-6 mx-auto flex flex-wrap flex-col md:flex-row items-center bg-yellow-50">
            <!-- 左側 -->
            <div class="flex flex-col w-full xl:w-2/5 justify-center lg:items-start overflow-y-hidden">
                <h1 class="my-4 text-3xl md:text-5xl text-green-800 font-bold leading-tight text-center md:text-left slide-in-bottom-h1">プログラマのかんたんSNS</h1>
                <p class="leading-normal text-base md:text-2xl mb-8 text-center md:text-left slide-in-bottom-subtitle">
                    仕事や勉強の合間に、趣味を公開したり共有したりするプログラミングの話題もウェルカム♪
                </p>

                <p class="text-blue-400 font-bold pb-8 lg:pb-6 text-center md:text-left fade-in">
                    会員募集中。お気軽にひょっこりきてください。
                </p>
                <div class="flex w-full justify-center md:justify-start pb-24 lg:pb-0 fade-in">
                    <a href="{{route('contact.create')}}"><button class="btnsetg">お問い合わせ</button></a>
                    <a href="{{route('register')}}"><x-primary-button class="btnsetr">ご登録はこちら</x-primary-button></a>
                </div>
            </div>
            <!-- 右側 -->
            <div class="w-full xl:w-3/5 py-6 overflow-y-hidden">
                <img class="w-5/6 mx-auto lg:mr-0 slide-in-bottom rounded-lg shadow-xl" src="{{secure_asset('logo/22940473.jpg')}}">
            </div>
        </div>
        <div class="container pt-10 md:pt-18 px-6 mx-auto flex flex-wrap flex-col md:flex-row items-center">
            <div class="w-full text-sm text-center md:text-left fade-in border-2 p-4 text-red-800 leading-8 mb-8">
                <P>自分と同じ趣味の人と語り合いたい、自分の趣味を披露したいと思う事はないですか？<br>本サイトでは、趣味の合うプログラマ同士の交流を深めていただけます。<br>お気軽に登録して、発信してみてくださいね。</p>
            </div>
            <!--フッタ-->
            <div class="w-full pt-10 pb-6 text-sm md:text-left fade-in">
                <p class="text-gray-500 text-center">@2023 ポートフォリオ用作品</p>
            </div>
        </div>
    </div>
</x-guest-layout>
