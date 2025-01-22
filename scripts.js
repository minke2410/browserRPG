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

/**
/**
 * 
 * 
 * モーダルにメッセージを表示し、閉じた際にリダイレクトを実行
 * @param {string} message 表示するメッセージ
 * @param {string} redirectUrl リダイレクト先のURL
 */
function showModalWithMessage(message, redirectUrl) {
  const modalMessageElement = document.getElementById('modal-message');
  if (modalMessageElement) {
    modalMessageElement.textContent = message; // メッセージを設定
  }

  const modalElement = document.getElementById('myModal');
  const modal = new bootstrap.Modal(modalElement);

  // 現在のセクションを取得
  const urlParams = new URLSearchParams(window.location.search);
  const currentSection = urlParams.get('section') || 'characters'; // デフォルトは'characters'

  // モーダルが閉じられたときにリダイレクトを実行
  modalElement.addEventListener('hidden.bs.modal', () => {
    if (redirectUrl) {
      window.location.href = `${redirectUrl}?section=${currentSection}`;
    }
  });

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
        // 変更･削除ボタンを表示
        document.getElementById('change-party-btn').style.display = 'inline-block';
        document.getElementById('delete-party-btn').style.display = 'inline-block';
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
      showModalWithMessage('出場パーティを変更しました！', 'game-main.php');
      // location.reload(); // 成功したらページをリロード
    } else {
      alert('エラー: ' + result.message);
    }
  } catch (error) {
    console.log('エラーが発生しました:', error);
    alert('通信エラーが発生しました。');
  }
}



// 選択したパーティーを削除する
async function deleteParty() {
  if (!window.selectedPartyId) {
    alert('削除するパーティーが選択されていません。');
    return;
  }

  // 削除の確認
  if (!confirm('本当にこのパーティーを削除しますか？')) {
    return;
  }


  try {
    const response = await fetch('delete-party.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ party_id: window.selectedPartyId })
    });

    const result = await response.json();

    if (result.success) {
      showModalWithMessage('選択したパーティーを削除しました｡', 'game-main.php');
      // location.reload(); // ページをリロード
    } else {
      alert('エラー: ' + result.message);
    }
  } catch (error) {
    console.error('通信エラー:', error);
    alert('通信エラーが発生しました。');
  }
}


let selectedDungeonId = null;

function selectDungeon(dungeonId) {
    // 既存の選択状態を解除
    document.querySelectorAll('.dungeon-box').forEach(box => box.classList.remove('selected'));

    // 新たに選択状態を設定
    const selectedDungeon = document.getElementById(`dungeon-${dungeonId}`);
    if (selectedDungeon) {
        selectedDungeon.classList.add('selected');
        selectedDungeonId = dungeonId;

        // 挑戦ボタンを表示
        document.getElementById('challenge-dungeon-btn').style.display = 'block';
    }
}

async function startChallenge() {
  if (!selectedDungeonId) {
      alert('ダンジョンを選択してください。');
      return;
  }

  try {
      const response = await fetch('check-dungeon-level.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ dungeon_id: selectedDungeonId })
      });

      const result = await response.json();

      if (result.success) {
          window.location.href = `battle-main.php?dungeon_id=${selectedDungeonId}`;
      } else {
          const modalMessage = document.getElementById('modal-message');
          modalMessage.textContent = result.message;

          const modal = new bootstrap.Modal(document.getElementById('myModal'));
          modal.show();
      }
  } catch (error) {
      console.error('エラーが発生しました:', error);
      alert('通信エラーが発生しました。');
  }
}


function challengeDungeon(dungeonId) {
  // ダンジョンIDをURLに含めてリダイレクト
  window.location.href = `battle-main.php?dungeon_id=${dungeonId}`;
}


function showPartyRegistrationForm() {
  const formContainer = document.getElementById('party-registration-form-container');
  formContainer.classList.add('show');
}

function hidePartyRegistrationForm() {
  const formContainer = document.getElementById('party-registration-form-container');
  formContainer.classList.remove('show');
}

async function submitPartyRegistration() {
  const form = document.getElementById('party-registration-form');
  const formData = new FormData(form);

  try {
    const response = await fetch('register-party.php', {
      method: 'POST',
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      showModalWithMessage('パーティが登録されました！', 'game-main.php');
      // location.reload(); // ページをリロードして更新
    } else {
      alert('エラー: ' + result.message);
    }
  } catch (error) {
    console.error('通信エラーが発生しました:', error);
    alert('通信エラーが発生しました。');
  }
}

async function checkPartyLimit() {
  try {
      const response = await fetch('check-party-limit.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
      });

      const result = await response.json();

      if (result.success) {
          // 制限以内ならフォームを表示
          showPartyRegistrationForm();
      } else {
          // 制限を超えている場合はモーダルを表示
          showModalWithMessage(result.message, 'game-main.php');
      }
  } catch (error) {
      console.error('通信エラーが発生しました:', error);
      alert('通信エラーが発生しました。');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const selects = document.querySelectorAll('.character-select');

  selects.forEach(select => {
    select.addEventListener('change', () => {
      // 選択済みの値を取得
      const selectedValues = Array.from(selects)
        .map(s => s.value)
        .filter(val => val !== ''); // 空の選択肢を除外

      // 各selectのオプションを更新
      selects.forEach(s => {
        Array.from(s.options).forEach(option => {
          if (selectedValues.includes(option.value) && s.value !== option.value) {
            option.style.display = 'none'; // 選択済みの値は非表示
          } else {
            option.style.display = ''; // それ以外は表示
          }
        });
      });
    });
  });
});
