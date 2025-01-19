// セクション表示制御
function showSection(sectionId) {
  // すべてのセクションを非表示にする
  document.querySelectorAll('#content > div').forEach(section => {
      section.classList.add('hidden');
  });

  // 指定されたセクションを表示
  const activeSection = document.getElementById(sectionId);
  if (activeSection) {
      activeSection.classList.remove('hidden');
  }

  // すべてのボタンから「active」クラスを削除
  document.querySelectorAll('.nav-bar button').forEach(button => {
      button.classList.remove('active');
  });

  // 対応するボタンに「active」クラスを追加
  const activeButton = document.querySelector(`.nav-bar button[onclick="showSection('${sectionId}')"]`);
  if (activeButton) {
      activeButton.classList.add('active');
  }

  // URLのセクションパラメータを更新
  const url = new URL(window.location.href);
  url.searchParams.set('section', sectionId);
  history.pushState({}, '', url);
}

// ページ読み込み時に初期化処理を実行
window.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const section = urlParams.get('section') || 'characters';
  showSection(section);
});



// キャラクターの詳細情報をトグル（表示/非表示）する関数
function toggleCharacterDetails(characterId) {
  // すべてのキャラクター詳細を取得
  const allDetails = document.querySelectorAll('.character-details');
  
  // 他のキャラクターの詳細情報を非表示にする
  allDetails.forEach(detail => {
    // 他のキャラクターの詳細が表示されていれば非表示にする
    if (detail.id !== 'details-' + characterId) {
      detail.style.display = 'none';
    }
  });

  // クリックされたキャラクターの詳細をトグル
  const detailsDiv = document.getElementById('details-' + characterId);
  
  // 詳細情報の表示状態を切り替え
  if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
    detailsDiv.style.display = 'block';
  } else {
    detailsDiv.style.display = 'none';
  }
}

// // モーダル表示
// function showModal(message) {
//   const modal = document.getElementById('modal');
//   const modalMessage = document.getElementById('modal-message');
//   modalMessage.textContent = message;  // モーダルにメッセージを表示
//   // modal.style.display = 'block';  // モーダルを表示

//   // 初回のみイベントリスナーを登録
//   // if (!modal.hasAttribute('data-listeners')) {
//     const closeModal = document.getElementById('modal-close');
//     closeModal.addEventListener('click', () => hideModal(modal));

//     window.addEventListener('click', function(event) {
//       if (event.target === modal) {
//         hideModal(modal);
//       }
//     });

//     modal.setAttribute('data-listeners', 'true'); // イベントリスナーが登録済みであることを記録
//   // }

//   // モーダルを表示
//   modal.classList.add('show');



//   // 一定時間後に自動でモーダルを閉じる (例: 3秒)
//   setTimeout(() =>  hideModal(modal), 2000);
// }

// function hideModal(modal) {
//   modal.classList.remove('show'); // 表示クラスを削除

//   // トランジションが終わるタイミングでdisplay: noneを設定
//   setTimeout(() => {
//     modal.style.display = 'none';
//   }, 3000); // CSSのトランジション時間と同じに設定
// }

function showModal() {
  // モーダルの要素を取得
  const modalElement = document.getElementById('myModal');

  // Bootstrapモーダルのインスタンスを作成
  const modal = new bootstrap.Modal(modalElement);

  // モーダルを表示
  modal.show();
}


    
// パーティーがクリックされたときに選択状態を切り替え
function selectParty(partyId) {
    // すでに選択されているパーティーがある場合、その枠を外す
    const previousSelectedParty = document.querySelector('.party-item.selected');
    if (previousSelectedParty) {
        previousSelectedParty.classList.remove('selected');
    }

    // クリックされたパーティーを枠で囲む
    const partyItem = document.getElementById('party-' + partyId);
    if (partyItem) {
        partyItem.classList.add('selected');
        // 選択されたパーティーIDを保存
        window.selectedPartyId = partyId;
        // 変更ボタンを表示
        document.getElementById('change-party-btn').style.display = 'inline-block';
    } else {
      console.error(`Party with ID ${partyId} not found.`);
    } 
}



// レスポンスのJSONをパースする関数
async function parseResponseJson(response) {
  console.log('Response Object:', response); // レスポンスオブジェクト全体を確認

  const text = await response.text();  // レスポンスを文字列として取得
  console.log('Response Text:', text);  // レスポンス内容を確認

  try {
    const result = JSON.parse(text);   // JSONとして解析
    console.log('Response JSON:', result); // 解析した結果を表示
    return result;
  } catch (error) {
    console.log('レスポンスはJSONではありません:', error);
    throw new Error('レスポンスがJSON形式ではありません');
  }
}


// 「パーティーを変更する」ボタンを押したときにサーバーに送信する
async function changeParty() {
  if (!window.selectedPartyId) {
    alert('パーティーが選択されていません。');
    return;
  }
  
  try {
    console.log('Sending fetch request...');
    const response = await fetch('update_active_party.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ party_id: window.selectedPartyId })
    });

    console.log('Response received:', response);

    // レスポンスのJSONをパース
    const result = await parseResponseJson(response);

    if (result.success) {
      showModal('パーティーがアクティブに設定されました！');
      // location.reload(); // 成功したらページをリロード
    } else {
      alert('エラー: ' + result.message);
    }
  } catch (error) {
    console.log('エラーが発生しました:', error);
    alert('通信エラーが発生しました。');
  }
}

function showDungeonDetails(dungeonId) {
  fetch(`get_dungeon_details.php?id=${dungeonId}`)
      .then(response => response.json())
      .then(data => {
          const detailsContainer = document.getElementById('dungeon-details');
          detailsContainer.innerHTML = `
              <h4>${data.name}</h4>
              <p>必要レベル: ${data.required_level}</p>
              <p>フロア数: ${data.floor}</p>
              <p>最大敵数: ${data.max_enemies}</p>
              <button onclick="challengeDungeon(${data.id})">挑戦する</button>
          `;
          detailsContainer.style.display = 'block';
      })
      .catch(error => console.error('Error:', error));
}
