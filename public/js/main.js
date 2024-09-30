document.addEventListener('DOMContentLoaded', function () {
    let errorCount = 0, score = 0, currentExerciseIndex = 0, exerciseData, userAnswers = [];
    const errorCountSpan = document.getElementById('error-count-span');
    const resultsDiv = document.querySelector('.results');
    const codeArea = document.getElementById('codeArea');
    const submitButton = document.querySelector('.submit-button');
    const skipButton = document.querySelector('.skip-button');
    const choicesDiv = document.getElementById('choices');
    const quizApp = document.getElementById('quizApp');
    const setupDiv = document.getElementById('setup');
    const inputContainer = document.getElementById('ftb-input');
    if (!inputContainer) {
        console.error('Div with class "ftb-input" not found.');
    } else {
        console.log('Div with class "ftb-input" found:', inputContainer);
    }
   
    let editor = ace.edit("codeArea");
    editor.setTheme("ace/theme/monokai");
    editor.session.setMode("ace/mode/javascript");
    
    const updateUI = (exercise) => {
        const sujetModule = exercise.sujet?.module || 'Unknown Subject';
        const niveau = exercise.niveau || 'Unknown Level';
        document.getElementById('category-span').textContent = sujetModule;
        document.getElementById('level-span').textContent = niveau;
        document.getElementById('question').textContent = exercise.contenu[0] || 'No content available';
    
        resultsDiv.textContent = '';
        errorCountSpan.textContent = errorCount;
        choicesDiv.innerHTML = '';
        editor.container.style.display = 'none';
    
        switch (exercise.type) {
            case 'QCM':
                renderMultipleChoice(exercise);
                break;
            case 'FTB':
                renderFillTheBlank(exercise);
                break;
            case 'OQ':
                renderOpenQuestion(exercise);
                break;
            default:
                console.error('Unsupported exercise type:', exercise.type);
        }
    };

    function renderMultipleChoice(exercise) {
        editor.container.style.display = 'none';
        choicesDiv.style.display = 'block';
    
        if (!Array.isArray(exercise.contenu) || exercise.contenu.length <= 1) {
            choicesDiv.innerHTML = "No valid options available for this question.";
            console.error("Invalid content for QCM:", exercise);
            return;
        }
    
        choicesDiv.innerHTML = exercise.contenu.slice(1).map((choice, index) => {
            return `<label><input type="radio" name="answer" value="${choice}">${choice}</label><br>`;
        }).join('');
    }

    function renderMultipleChoice(exercise) {
        const editor = ace.edit("codeArea");
        editor.container.style.display = 'none';
        choicesDiv.style.display = 'block';
    
        if (!Array.isArray(exercise.contenu) || exercise.contenu.length <= 1) {
            choicesDiv.innerHTML = "No valid options available for this question.";
            console.error("Invalid content for QCM:", exercise);
            return;
        }
    
        choicesDiv.innerHTML = exercise.contenu.slice(1).map((choice, index) => {
            return `<label><input type="radio" name="answer" value="${choice}">${choice}</label><br>`;
        }).join('');
    }

    function renderFillTheBlank(exercise) {
        choicesDiv.innerHTML = '';
        const question = exercise.contenu && exercise.contenu.length > 0 ? exercise.contenu[0] : "Question missing";
        document.getElementById('question').textContent = question;
    
        /*if (!exercise.contenu || exercise.contenu.length < 2) {
            choicesDiv.innerHTML = "Not enough information for fill-in-the-blanks.";
            return;
        }*/
    
        const codeSnippet = exercise.contenu[0];
        console.log(codeSnippet);
        const pre = document.createElement('pre');
        const code = document.createElement('code');
        code.className = 'language-javascript';
    
        const parts = codeSnippet.split(/___/g);
        let codeContent = parts.map((part, index) => {
            return part + (index < parts.length - 1 ? '___' : '');
        }).join('');

        console.log(parts);
    
        code.innerHTML = codeContent;
        pre.appendChild(code);
        //choicesDiv.appendChild(pre);
    
      
        inputContainer.innerHTML = ''; // Clear any previous inputs
    
        parts.forEach((part, index) => {
            if (index < parts.length - 1) {
                const inputSize = exercise.solution[index].length;
                const input = document.createElement('input');
                input.type = 'text';
                input.className = `ftb-input ftb-input-${index}`;
                input.name = `ftb-input-${index}`; // Add a name for easier search
                input.style.width = `${inputSize * 10}px`;
                input.maxLength = inputSize;
                input.placeholder = 'Enter answer';
                inputContainer.appendChild(input);
            }
        });
    
        hljs.highlightElement(code);
    
        const editor = ace.edit("codeArea");
        editor.container.style.display = 'none';
        inputContainer.style.display = 'block';
        choicesDiv.style.display = 'block';
    }
    
    
    
    

    function renderOpenQuestion(exercise) {
        let editor = ace.edit("codeArea");
        editor.setTheme("ace/theme/monokai");
        editor.session.setMode("ace/mode/javascript");
        choicesDiv.style.display = 'none';
        editor.container.style.display = 'block';
        
        editor.setValue("", -1);
    }

    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    const fetchExerciseData = () => {
        fetch('/api/exercises')
            .then(response => response.json())
            .then(data => {
                exerciseData = data.filter(ex => {
                    return ex.sujet.module === document.getElementById('subject').value &&
                           ex.niveau === document.getElementById('level').value;
                });

                exerciseData = shuffleArray(exerciseData).slice(0, parseInt(document.getElementById('numQuestions').value, 10));

                if (exerciseData.length) {
                    currentExerciseIndex = 0;
                    updateUI(exerciseData[currentExerciseIndex]);
                    setupDiv.style.display = 'none';
                    quizApp.style.display = 'block';
                } else {
                    console.error('No exercises match the selected criteria.');
                }
            })
            .catch(error => {
                console.error('Error loading the exercises:', error);
                alert('Failed to load exercises. Check the console for details.');
            });
    };

    window.startQuiz = () => {
        errorCount = 0;
        score = 0;
        currentExerciseIndex = 0;
        fetchExerciseData();
    };

    submitButton.addEventListener('click', () => {
        const exercise = exerciseData[currentExerciseIndex];
        let userAnswer, isSolutionCorrect;
    
        switch (exercise.type) {
            case 'QCM':
                const selectedOption = document.querySelector('input[name="answer"]:checked');
                userAnswer = selectedOption ? selectedOption.value : '';
                isSolutionCorrect = userAnswer === exercise.solution[0];
                resultsDiv.innerHTML = isSolutionCorrect ? "" : `<strong>Incorrect answer. Correct answer:</strong> ${exercise.solution[0]}`;
                score += isSolutionCorrect ? 1 : 0;
                break;
    
            case 'FTB':
                userAnswer = Array.from(document.querySelectorAll('.ftb-input')).filter(input => input.value !== undefined).map(input => input.value.trim());
                isSolutionCorrect = arraysEqual(userAnswer, exercise.solution);
                errorCount = isSolutionCorrect ? 0 : (errorCount + 1);
                errorCountSpan.textContent = errorCount;
                resultsDiv.innerHTML = isSolutionCorrect ? "" : `<strong>Incorrect, correct answer:</strong> ${exercise.solution.join(' ')}`;
                score += isSolutionCorrect ? 1 : 0;
                break;
    
            case 'OQ':
                userAnswer = editor.getValue().trim();
                fetch('https://127.0.0.1:8000/run_code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ code: userAnswer })
                })
                .then(response => response.json())
                .then(data => {
                    isSolutionCorrect = containsSequence(data.output, exercise.solution[0]);
                    processAnswer(isSolutionCorrect, exercise, data.output);
                })
                .catch(error => {
                    console.error('Error executing the code:', error);
                    resultsDiv.textContent = 'Error executing the code.';
                });
                return;
        }
    
        userAnswers[currentExerciseIndex] = userAnswer;
        if (exercise.type !== 'OQ') moveToNextQuestion();
    });
    
   
    
    function containsSequence(actualOutput, expectedOutput) {
        const normalizedActual = normalizeOutput(actualOutput);
        const normalizedExpected = normalizeOutput(expectedOutput);
        
        return normalizedActual.includes(normalizedExpected);
    }

    function arraysEqual(arr1, arr2) {
        if (arr1.length !== arr2.length) {
            return false;
        }
        for (let i = 0; i < arr1.length; i++) {
            if (arr1[i] !== arr2[i]) {
                return false;
            }
        }
        return true;
    }
    
    function normalizeOutput(output) {
        if (Array.isArray(output)) {
            return `[${output.join(',')}]`; // Convert array to a string with no spaces
        }
        return output.replace(/\s+/g, '').trim(); // Remove spaces for strings and trim
    }
    
    function processAnswer(isCorrect, exercise, output) {
        let userAnswer = editor.getValue();
        userAnswers[currentExerciseIndex] = userAnswer;
        const isSolutionCorrect = normalizeOutput(output) === normalizeOutput(exercise.solution[0]);
        console.log("output=", normalizeOutput(output), "expected=",normalizeOutput(exercise.solution[0]))
        //console.log("Processing answer. Is correct:", isCorrect, "User answer:", userAnswer, "Output:", output);
    
        if (isSolutionCorrect) {
            score++;
            moveToNextQuestion();
        } else {
            errorCount++;
            errorCountSpan.textContent = errorCount;
            resultsDiv.innerHTML = `<strong>Incorrect output:</strong><pre><code class="language-javascript">${output}</code></pre><strong>Expected output:</strong><pre><code class="language-javascript">${exercise.solution[0]}</code></pre>`;
            resultsDiv.classList.add("resultado");
            if (errorCount >= 3) {
                resultsDiv.innerHTML += `<br><strong>Correction:</strong><pre><code class="language-javascript">${exercise.solution[1]}</code></pre>`;
                setTimeout(moveToNextQuestion, 15000); // Short delay before moving to the next question
                errorCount = 0;
            }
            hljs.highlightAll();
        }
    }

    skipButton.addEventListener('click', () => {
       // console.log("Skipping question.");
        moveToNextQuestion();
    });

    const moveToNextQuestion = () => {
       // console.log("Moving to next question. Current index:", currentExerciseIndex);
        if (currentExerciseIndex + 1 < exerciseData.length) {
            currentExerciseIndex++;
            updateUI(exerciseData[currentExerciseIndex]);
        } else {
            console.log("Quiz complete. Saving score...");
            savescore().then(() => {
                displayFinalResults();
            }).catch(error => {
                console.error('Failed to save score:', error);
            });
        }
    };

    const displayFinalResults = () => {
       // console.log("Displaying final results. Score:", score, "User answers:", userAnswers);
        quizApp.style.display = 'none';
        localStorage.setItem('results', JSON.stringify(exerciseData));
        localStorage.setItem('userAnswers', JSON.stringify(userAnswers));
        localStorage.setItem('score', score);
        window.location.href = '/results';
    };

    const savescore = () => {
        return new Promise((resolve, reject) => {
            const exerciceIds = exerciseData.map(ex => ex.id);
            const currentExercise = exerciseData[currentExerciseIndex];
            const sujetId = currentExercise.sujet.id;
            const niveau = currentExercise.niveau;
            const prct = (score / exerciseData.length) * 100;

            const scoreData = {
                note: prct,
                sujetId: sujetId,
                exerciceIds: exerciceIds,
                niveau: niveau
            };

       //    console.log("Saving score data:", scoreData);

            fetch('/api/score', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(scoreData)
            })
            .then(response => response.json())
            .then(data => {
            //    console.log('Score saved successfully:', data);
                resolve(data);
            })
            .catch((error) => {
                console.error('Error saving score:', error);
                reject(error);
            });
        });
    };
});

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
                const userAnswer = userAnswers[index] || "Pas de Réponse Saisie";
                const correct = isAnswerCorrect(exercise, userAnswer);
                const color = correct ? 'green' : 'red';
                return `<div>
                    <p>Question ${index + 1}: ${exercise.contenu.join(" ")}</p>
                    <p>Expected Answer/Output: ${exercise.solution.join(' ')}</p>
                    <p style="color:${color};">Votre Réponse / Output: ${Array.isArray(userAnswer) ? userAnswer.join(' ') : userAnswer}</p>
                </div><br>`;
            }).join('');
        }
    };

    function isAnswerCorrect(exercise, userAnswer) {
        if (exercise.type === 'FTB' || Array.isArray(exercise.solution)) {
            return arraysEqual(exercise.solution, userAnswer);
        } else {
            return containsSequence(normalizeOutput(userAnswer), normalizeOutput(exercise.solution[0]));
        }
    }

    function arraysEqual(arr1, arr2) {
        if (arr1.length !== arr2.length) return false;
        for (let i = 0; i < arr1.length; i++) {
            if (arr1[i] !== arr2[i]) return false;
        }
        return true;
    }

    
    
   /* function areOutputsEquivalent(output1, output2) {
        const normalizedOutput1 = normalizeOutput(output1);
        const normalizedOutput2 = normalizeOutput(output2);
        return normalizedOutput1 === normalizedOutput2;
    }*/
});
