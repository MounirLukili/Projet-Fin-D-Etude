document.addEventListener('DOMContentLoaded', () => {
    const scoreDisplay = document.getElementById('scoreDisplay');
    const detailsButton = document.getElementById('detailsButton');
    const detailsDiv = document.getElementById('details');

    let results = JSON.parse(localStorage.getItem('results'));

    if (!Array.isArray(results)) {
        console.error("Results is not an array:", results);
        results = [];
    }

    const userAnswers = JSON.parse(localStorage.getItem('userAnswers'));
    const score = localStorage.getItem('score');

    scoreDisplay.innerHTML = `<strong>Votre Score Final est: ${score}/${results.length}</strong>`;

    detailsButton.onclick = () => {
        const isVisible = detailsDiv.style.display === 'block';
        detailsDiv.style.display = isVisible ? 'none' : 'block';
        if (!isVisible) {
            if (results.length !== userAnswers.length) {
                console.error("Mismatch between results and user answers length");
            }

            detailsDiv.innerHTML = results.map((exercise, index) => {
                let solution = '';
                if (exercise.type === 'QCM' || exercise.type === 'FTB') {
                    solution = exercise.solution.join(' '); // Join solution array into a string
                } else if (exercise.type === 'OQ') {
                    solution = exercise.solution[1];
                }

                return `<div class="yow">
                    <p>Question ${index + 1}: ${exercise.contenu.join(" ")}</p>
                    <p>Correction:</p>
                    <pre><code class="language-javascript">${solution}</code></pre>
                </div><br>`;
            }).join('');

            // Apply syntax highlighting
            hljs.highlightAll();
        }
    };
});
