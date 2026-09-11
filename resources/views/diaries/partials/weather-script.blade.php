<script>
    document.addEventListener('DOMContentLoaded', function () {
        const button = document.getElementById(
            'fetch-weather'
        );

        if (!button) {
            return;
        }

        button.addEventListener('click', async function () {
            const date = document
                .getElementById('activity_date')
                .value;

            const message = document
                .getElementById('weather-message');

            if (!date) {
                message.textContent =
                    '先に日付を入力してください。';
                message.style.color = '#dc2626';

                return;
            }

            button.disabled = true;
            message.textContent =
                '天気を取得しています…';
            message.style.color = '#4b5563';

            const query = new URLSearchParams({
                date: date,
            });

            try {
                const response = await fetch(
                    '{{ route('weather.show') }}'
                    + '?'
                    + query.toString(),
                    {
                        headers: {
                            Accept: 'application/json',
                        },
                    }
                );

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(
                        result.message
                        ?? '天気を取得できませんでした。'
                    );
                }

                document.getElementById('weather').value =
                    result.data.weather;

                document
                    .getElementById('sunrise_time')
                    .value = result.data.sunrise_time;

                document
                    .getElementById('sunset_time')
                    .value = result.data.sunset_time;

                document
                    .getElementById('weather_was_fetched')
                    .value = '1';

                message.textContent =
                    '天気情報を取得しました。'
                    + '必要に応じて修正できます。';

                message.style.color = '#166534';
            } catch (error) {
                document
                    .getElementById('weather_was_fetched')
                    .value = '0';

                message.textContent = error.message;
                message.style.color = '#dc2626';
            } finally {
                button.disabled = false;
            }
        });
    });
</script>