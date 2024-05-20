 <h3>Ceci est une API de raccourcissement de liens html</h3>
        <h6>
            <h2>Introduction</h2>
            <p>Cette API permet de raccourcir des URLs en générant des liens courts personnalisés. Elle est conçue pour être simple à utiliser et peut être intégrée dans divers projets web.</p>
            <h2>Utilisation</h2>
            <ol>
                <li><strong>Obtenir un token d'accès</strong>: Vous devez obtenir un token d'accès pour utiliser l'API. Ce token peut être obtenu en vous inscrivant sur notre plateforme.</li>
                <li><strong>Envoyer une requête POST</strong>: Vous devez envoyer une requête POST à l'endpoint de l'API avec les données suivantes au format JSON :</li>
            </ol>
            <p></p>
            <pre><code>{
            "token": "votre_token_d_acces",
            "url": "URL_a_raccourcir"
        }</code></pre>
            <ol start="3">
                <li><strong>Réponse de l'API</strong>: L'API renverra une réponse au format JSON qui contient soit l'URL raccourcie, soit un message d'erreur si une erreur survient.</li>
            </ol>        
            <h2>Exemple</h2>
            <p>Voici un exemple d'utilisation de l'API en utilisant cURL en ligne de commande :</p>
            <pre><code>curl -X POST \
          -H "Content-Type: application/json" \
          -d '{"token": "votre_token_d_acces", "url": "https://example.com/page"}' \
          https://serveur/api</code></pre>        
            <h2>Réponses de l'API</h2>      
            <h3>Succès</h3>
            <pre><code>{
            "short_url": "https://serveur/abcdef"
        }</code></pre>        
            <h3>Erreur</h3>
            <pre><code>{
            "error": "Message d'erreur correspondant à la situation rencontrée"
        }</code></pre>        
            <h2>Limitations</h2>
            <ul>
                <li>Seules les requêtes POST sont acceptées.</li>
                <li>Les données doivent être envoyées au format JSON.</li>
                <li>L'URL doit être valide et accessible.</li>
            </ul>        
            <h2>Conclusion</h2>
            <p>Vous pouvez maintenant utiliser cette API pour raccourcir vos URLs dans vos projets web. Pour toute question ou assistance supplémentaire, n'hésitez pas à nous contacter.</p>   
        </h6>
